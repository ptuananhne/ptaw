/**
 * ===============================================================================
 * == FILE JAVASCRIPT HOÀN CHỈNH - PHIÊN BẢN 8.0
 * == Tác giả: Gemini
 * == Ngày: 2025-07-09
 * ==
 * == Mô tả:
 * == - Khôi phục đầy đủ logic cho các chức năng toàn trang đã bị mất:
 * ==   + Slider sản phẩm (kéo/thả).
 * ==   + Slider banner (tự động chạy).
 * == - Giữ nguyên và tích hợp logic mới cho trang chi tiết sản phẩm V2.
 * == - Cấu trúc lại code thành các hàm riêng biệt, dễ quản lý và chỉ chạy khi cần.
 * ===============================================================================
 */
document.addEventListener("DOMContentLoaded", function () {

  // --- KHỞI TẠO CÁC CHỨC NĂNG CHUNG ---
  initializeMobileMenu();
  document.querySelectorAll(".product-slider").forEach(initializeProductSlider);
  initializeBannerSlider();

  // --- KHỞI TẠO LOGIC CHO TRANG CHI TIẾT SẢN PHẨM CŨ (V1) ---
  const productDetailPageV1 = document.querySelector('.product-detail-page');
  if (productDetailPageV1 && !productDetailPageV1.classList.contains('product-detail-page-v2')) {
    initializeProductGalleryV1(productDetailPageV1);
    initializeFacebookButton(productDetailPageV1);
  }

  // --- KHỞI TẠO LOGIC CHO TRANG CHI TIẾT SẢN PHẨM MỚI (V2) ---
  const productDetailPageV2 = document.querySelector('.product-detail-page-v2');
  if (productDetailPageV2) {
    initializeProductGalleryV2(productDetailPageV2);
    initializeProductTabsV2(productDetailPageV2);
  }
  
  // --- KHỞI TẠO LOGIC CHỌN PHIÊN BẢN (DÙNG CHUNG CHO CẢ V1 & V2) ---
  if (document.querySelector("[data-product-type='variable']")) {
    initializeProductVariantLogic();
  }


  // =============================================================================
  // == CÁC HÀM KHỞI TẠO (INITIALIZER FUNCTIONS)
  // =============================================================================

  /**
   * --- LOGIC MENU DI ĐỘNG ---
   * Điều khiển việc đóng/mở menu trên thiết bị di động.
   */
  function initializeMobileMenu() {
    const menuToggle = document.getElementById("mobile-menu-toggle");
    const sidebar = document.getElementById("sidebar-mobile");
    const overlay = document.getElementById("menu-overlay");
    const closeBtn = document.getElementById("sidebar-close-btn");

    if (!menuToggle || !sidebar || !overlay || !closeBtn) return;

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
   * --- LOGIC SLIDER SẢN PHẨM (KÉO THẢ) - ĐÃ KHÔI PHỤC ĐẦY ĐỦ ---
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
  
  /**
   * --- LOGIC SLIDER BANNER (TỰ ĐỘNG CHẠY) - ĐÃ KHÔI PHỤC ĐẦY ĐỦ ---
   */
  function initializeBannerSlider() {
    const bannerSlider = document.querySelector(".banner-slider-container");
    if (!bannerSlider) return;
    
    const track = bannerSlider.querySelector(".banner-track");
    if (!track) return;

    const slides = Array.from(track.children);
    if (slides.length <= 1) return;

    const nextButton = bannerSlider.querySelector(".banner-btn.next");
    const prevButton = bannerSlider.querySelector(".banner-btn.prev");
    let currentIndex = 0;
    let intervalId;

    const moveToSlide = (targetIndex) => {
      if (targetIndex >= slides.length) targetIndex = 0;
      if (targetIndex < 0) targetIndex = slides.length - 1;

      const slideWidth = slides[0].getBoundingClientRect().width;
      track.style.transform = `translateX(-${slideWidth * targetIndex}px)`;
      currentIndex = targetIndex;
    };

    const startAutoPlay = () => {
      stopAutoPlay();
      intervalId = setInterval(() => moveToSlide(currentIndex + 1), 5000);
    };

    const stopAutoPlay = () => clearInterval(intervalId);

    if (prevButton && nextButton) {
      prevButton.addEventListener("click", () => moveToSlide(currentIndex - 1));
      nextButton.addEventListener("click", () => moveToSlide(currentIndex + 1));
    }

    bannerSlider.addEventListener("mouseenter", stopAutoPlay);
    bannerSlider.addEventListener("mouseleave", startAutoPlay);

    new ResizeObserver(() => moveToSlide(currentIndex)).observe(bannerSlider);

    startAutoPlay();
  }

  /**
   * --- LOGIC GALLERY ẢNH CHO TRANG CHI TIẾT SẢN PHẨM V1 (CŨ) ---
   */
  function initializeProductGalleryV1(container) {
    const mainImage = container.querySelector("#main-product-image");
    const thumbnails = container.querySelectorAll(".thumbnail-item");

    if (!mainImage || thumbnails.length === 0) return;
    
    thumbnails.forEach((thumb) => {
      thumb.addEventListener("click", function () {
        thumbnails.forEach((t) => t.classList.remove("active"));
        this.classList.add("active");
        mainImage.src = this.querySelector("img").src;
      });
    });
  }

  /**
   * --- LOGIC NÚT FACEBOOK CHO TRANG CHI TIẾT SẢN PHẨM V1 (CŨ) ---
   */
  function initializeFacebookButton(container) {
    const fbButton = container.querySelector("#fb-messenger-btn");
    if (!fbButton) return;

    fbButton.addEventListener("click", function (e) {
      e.preventDefault();
      const productUrl = window.location.href;
      const originalText = this.querySelector("span").innerText;
      const textToCopy = `Chào bạn, tôi quan tâm đến sản phẩm này: ${productUrl}`;

      navigator.clipboard.writeText(textToCopy).then(() => {
        this.querySelector("span").innerText = "Đã sao chép link!";
        window.open(this.href, "_blank");
        setTimeout(() => {
          this.querySelector("span").innerText = originalText;
        }, 2500);
      }).catch((err) => {
        console.error("Không thể sao chép link: ", err);
        this.querySelector("span").innerText = "Lỗi sao chép";
        setTimeout(() => {
          this.querySelector("span").innerText = originalText;
        }, 2500);
      });
    });
  }

  /**
   * --- LOGIC GALLERY ẢNH CHO TRANG CHI TIẾT SẢN PHẨM V2 (MỚI) ---
   */
  function initializeProductGalleryV2(container) {
    const mainImage = container.querySelector("#main-product-image-v2");
    const thumbnails = container.querySelectorAll(".thumbnail-item-v2");

    if (!mainImage || thumbnails.length === 0) return;

    thumbnails.forEach((thumb) => {
      thumb.addEventListener("click", function () {
        const newImageSrc = this.querySelector("img").src;
        thumbnails.forEach((t) => t.classList.remove("active"));
        this.classList.add("active");
        mainImage.src = newImageSrc;
      });
    });
  }

  /**
   * --- LOGIC TABS THÔNG TIN CHO TRANG CHI TIẾT SẢN PHẨM V2 (MỚI) ---
   */
  function initializeProductTabsV2(container) {
    const tabLinks = container.querySelectorAll(".tab-link");
    const tabPanes = container.querySelectorAll(".tab-pane");

    if (tabLinks.length === 0 || tabPanes.length === 0) return;

    tabLinks.forEach(link => {
      link.addEventListener("click", () => {
        const tabId = link.dataset.tab;
        tabLinks.forEach(l => l.classList.remove("active"));
        link.classList.add("active");
        tabPanes.forEach(pane => {
          pane.classList.toggle("active", pane.id === tabId);
        });
      });
    });
  }

  /**
   * --- LOGIC CHỌN PHIÊN BẢN SẢN PHẨM (DÙNG CHUNG) ---
   */
  function initializeProductVariantLogic() {
    const container = document.querySelector(".product-detail-page-v2, .product-detail-page");
    if (!container) return;

    const variantsDataElement = document.getElementById("product-variants-data");
    const priceDisplay = document.getElementById("product-price-display");
    const mainImage = container.querySelector("#main-product-image-v2, #main-product-image");
    
    if (!variantsDataElement || !priceDisplay || !mainImage) return;

    let variants;
    try {
        variants = JSON.parse(variantsDataElement.textContent);
    } catch (e) {
        console.error("Lỗi đọc dữ liệu JSON của phiên bản:", e);
        return;
    }

    if (!variants || variants.length === 0) return;

    variants.forEach(v => {
      if (typeof v.attributes === 'string') {
        try {
          v.attributes = JSON.parse(v.attributes);
        } catch(e) {
          v.attributes = {};
        }
      }
    });

    const originalImageSrc = mainImage.src;
    const variantInputs = container.querySelectorAll('input[type="radio"]');
    const attributeGroups = Array.from(container.querySelectorAll('.variant-group-v2, .variant-group'));
    const attributeNames = attributeGroups.map(g => g.querySelector('.variant-label-v2, .variant-group-label').textContent.replace(':', '').trim());

    const formatPrice = (price) => (price > 0 ? `${parseInt(price).toLocaleString('vi-VN')} đ` : "Liên hệ");

    const getSelectedOptions = () => {
      const selected = {};
      attributeNames.forEach(name => {
        const checkedInput = container.querySelector(`input[name="attribute_${name}"]:checked`);
        if (checkedInput) selected[name] = checkedInput.value;
      });
      return selected;
    };

    const findMatchingVariant = (selectedOptions) => {
      if (Object.keys(selectedOptions).length !== attributeNames.length) return null;
      return variants.find(variant => 
        attributeNames.every(name => variant.attributes[name] === selectedOptions[name])
      );
    };
    
    const updateDisplay = () => {
      const selectedOptions = getSelectedOptions();
      const matchedVariant = findMatchingVariant(selectedOptions);

      if (matchedVariant) {
        priceDisplay.textContent = formatPrice(matchedVariant.price);
        if (matchedVariant.image_url) {
            mainImage.src = `${window.BASE_URL || ''}/${matchedVariant.image_url}`;
        } else {
            mainImage.src = originalImageSrc;
        }
      } else {
        priceDisplay.textContent = "Chọn phiên bản";
        mainImage.src = originalImageSrc;
      }
      updateAvailableOptions();
    };

    const updateAvailableOptions = () => {
        const selectedOptions = getSelectedOptions();
        variantInputs.forEach(input => {
            const currentAttr = input.dataset.attributeName;
            const testSelection = { ...selectedOptions, [currentAttr]: input.value };
    
            if (input.checked) {
                input.disabled = false;
                const label = input.closest('.variant-option-v2, .variant-option');
                if (label) label.classList.remove('disabled');
                return;
            }
    
            const isAvailable = variants.some(variant => {
                return Object.entries(testSelection).every(([key, value]) => {
                    if (!selectedOptions.hasOwnProperty(key) && key !== currentAttr) {
                        return true;
                    }
                    return variant.attributes[key] === value;
                });
            });
    
            input.disabled = !isAvailable;
            const label = input.closest('.variant-option-v2, .variant-option');
            if (label) {
                label.classList.toggle('disabled', !isAvailable);
            }
        });
    };

    container.addEventListener('change', (e) => {
        if(e.target.matches('input[type="radio"]')) {
            updateDisplay();
        }
    });
    
    updateDisplay();
  }
});
