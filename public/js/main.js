/**
 * ===============================================================================
 * == FILE JAVASCRIPT NÂNG CẤP - PHIÊN BẢN 6.0
 * == Tác giả: Gemini
 * == Ngày: 2025-07-06
 * ==
 * == Mô tả: Tinh chỉnh và tối ưu hóa JavaScript.
 * == - Cải thiện logic slider cho mượt mà hơn.
 * == - Thêm tính năng sao chép link sản phẩm vào clipboard một cách thân thiện.
 * == - Đảm bảo tất cả các script đều an toàn và chỉ chạy khi có phần tử HTML tương ứng.
 * ===============================================================================
 */
document.addEventListener("DOMContentLoaded", function () {
  /**
   * --- LOGIC MENU DI ĐỘNG ---
   * Điều khiển việc đóng/mở menu trên thiết bị di động.
   */
  const menuToggle = document.getElementById("mobile-menu-toggle");
  const sidebar = document.getElementById("sidebar-mobile");
  const overlay = document.getElementById("menu-overlay");
  const closeBtn = document.getElementById("sidebar-close-btn");

  if (menuToggle && sidebar && overlay && closeBtn) {
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

    menuToggle.addEventListener("click", (e) => {
      e.stopPropagation();
      openMenu();
    });

    overlay.addEventListener("click", closeMenu);
    closeBtn.addEventListener("click", closeMenu);
  }

  /**
   * --- LOGIC SLIDER SẢN PHẨM (KÉO THẢ) ---
   * Khởi tạo tất cả các slider sản phẩm trên trang.
   * Hỗ trợ kéo thả (drag) trên desktop và vuốt (swipe) trên mobile.
   */
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
      const isScrollable = maxScroll > 5; // Check if there's enough content to scroll

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

    // Prevent click on links after dragging
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
    // Use ResizeObserver to update buttons when viewport size changes
    new ResizeObserver(() => setTimeout(updateButtons, 150)).observe(track);
    setTimeout(updateButtons, 150); // Initial check
  }
  document.querySelectorAll(".product-slider").forEach(initializeProductSlider);

  /**
   * --- LOGIC SLIDER BANNER (TỰ ĐỘNG CHẠY) ---
   * Điều khiển banner chính ở trang chủ.
   */
  const bannerSlider = document.querySelector(".banner-slider-container");
  if (bannerSlider) {
    const track = bannerSlider.querySelector(".banner-track");
    const slides = Array.from(track.children);
    if (slides.length <= 1) return; // Don't initialize if only one or zero slides

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
      }, 5000); // Change slide every 5 seconds
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

  /**
   * --- LOGIC TRANG CHI TIẾT SẢN PHẨM ---
   * - Gallery ảnh sản phẩm.
   * - Nút "Nhắn qua Facebook" với tính năng sao chép link sản phẩm.
   */
  const productDetailPage = document.querySelector(".product-detail-page");
  if (productDetailPage) {
    // Gallery logic
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

    // Facebook Messenger button with copy-to-clipboard functionality
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
            window.open(this.href, "_blank"); // Open Messenger link
            setTimeout(() => {
              this.querySelector("span").innerText = originalText;
            }, 2500); // Revert text after 2.5 seconds
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
  const animatedElements = document.querySelectorAll("[data-animate]");

  if (animatedElements.length > 0) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            // Optional: unobserve after animation to save resources
            observer.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.1, // Kích hoạt khi 10% element hiện ra
      }
    );

    animatedElements.forEach((element) => {
      observer.observe(element);
    });
  }
});
