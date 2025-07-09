/**
 * ===============================================================================
 * == FILE JAVASCRIPT TỔNG HỢP - PHIÊN BẢN 9.1 (SỬA LỖI LOGIC)
 * == Tác giả: Gemini
 * == Ngày: 2025-07-09
 * ==
 * == Mô tả: 
 * == - Sửa lỗi logic biến thể bằng cách lấy tên thuộc tính từ data-attribute
 * ==   thay vì từ text label, đảm bảo so khớp dữ liệu chính xác.
 * == - Giữ nguyên và tối ưu toàn bộ chức năng cũ và mới.
 * ===============================================================================
 */
document.addEventListener("DOMContentLoaded", function () {
  
  /**
   * --- KHỞI TẠO CÁC CHỨC NĂNG TOÀN TRANG ---
   */
  initializeMobileMenu();
  document.querySelectorAll(".product-slider").forEach(initializeProductSlider);
  initializeBannerSlider();
  initializeProductDetailPageV2(); // Chứa logic mới
  initializeScrollAnimations();
  initializeMapTabs();

    const themeToggle = document.getElementById('theme-toggle');
  const htmlEl = document.documentElement;

  themeToggle.addEventListener('click', () => {
    if (htmlEl.hasAttribute('data-theme')) {
      htmlEl.removeAttribute('data-theme');
      localStorage.removeItem('theme');
    } else {
      htmlEl.setAttribute('data-theme', 'dark');
      localStorage.setItem('theme', 'dark');
    }
  });

  // Check for saved theme in localStorage
  if (localStorage.getItem('theme') === 'dark') {
    htmlEl.setAttribute('data-theme', 'dark');
  }
  /**
   * =============================================================================
   * == CÁC HÀM KHỞI TẠO (INITIALIZER FUNCTIONS)
   * =============================================================================
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
    menuToggle.addEventListener("click", (e) => { e.stopPropagation(); openMenu(); });
    overlay.addEventListener("click", closeMenu);
    closeBtn.addEventListener("click", closeMenu);
  }

  function initializeProductSlider(sliderElement) {
    const track = sliderElement.querySelector(".slider-track");
    const prevBtn = sliderElement.querySelector(".slider-btn.prev");
    const nextBtn = sliderElement.querySelector(".slider-btn.next");
    if (!track || !prevBtn || !nextBtn) return;
    let isDragging = false, startX, startScrollLeft, draggedDistance = 0;
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
    const stopDrag = () => { isDragging = false; track.classList.remove("active-drag"); };
    track.querySelectorAll("a").forEach(link => link.addEventListener("click", e => { if (draggedDistance > 10) e.preventDefault(); }));
    track.addEventListener("mousedown", startDrag);
    track.addEventListener("touchstart", startDrag, { passive: true });
    track.addEventListener("mousemove", onDrag);
    track.addEventListener("touchmove", onDrag, { passive: true });
    document.addEventListener("mouseup", stopDrag);
    document.addEventListener("touchend", stopDrag);
    const scrollAmount = () => track.clientWidth * 0.8;
    nextBtn.addEventListener("click", () => track.scrollBy({ left: scrollAmount(), behavior: "smooth" }));
    prevBtn.addEventListener("click", () => track.scrollBy({ left: -scrollAmount(), behavior: "smooth" }));
    track.addEventListener("scroll", updateButtons, { passive: true });
    new ResizeObserver(() => setTimeout(updateButtons, 150)).observe(track);
    setTimeout(updateButtons, 150);
  }

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
      if (slideWidth === 0) return;
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
   * --- LOGIC TRANG CHI TIẾT SẢN PHẨM V2 (Bao gồm logic biến thể thông minh) ---
   */
  function initializeProductDetailPageV2() {
    const page = document.querySelector('.product-detail-page-v2');
    if (!page) return;

    // --- 1. LOGIC CHUNG ---
    const mainImage = document.getElementById("main-product-image");
    const fbButton = page.querySelector("#fb-messenger-btn");
    const productTabsContainer = page.querySelector(".product-details-tabs-v2");

    // --- 2. LOGIC BIẾN THỂ (NÂNG CẤP) ---
    const variantsContainer = document.getElementById("product-variants-container");
    const variantsDataElement = document.getElementById("product-variants-data");
    const priceDisplay = document.getElementById("product-price-display");

    if (variantsContainer && variantsDataElement && mainImage && priceDisplay && page.dataset.productType === 'variable') {
      let variants;
      try {
        variants = JSON.parse(variantsDataElement.textContent);
        if (!variants || variants.length === 0) throw new Error("No variants data");
        variants.forEach(v => {
          if (typeof v.attributes === 'string') {
            try { v.attributes = JSON.parse(v.attributes); } 
            catch (e) { console.error("Lỗi parse JSON của biến thể:", v); v.attributes = {}; }
          }
        });
      } catch (e) {
        console.error("Lỗi khi xử lý dữ liệu biến thể:", e);
        return;
      }

      const originalImageSrc = mainImage.src;
      const allInputs = Array.from(variantsContainer.querySelectorAll('input[type="radio"]'));
      const attributeGroups = Array.from(variantsContainer.querySelectorAll('.variant-group-v2'));
      
      // **FIX**: Lấy tên thuộc tính từ data-attribute để đảm bảo chính xác
      const attributeNames = attributeGroups.map(g => g.dataset.attributeGroupName);

      const formatPrice = (price) => price > 0 ? `${parseInt(price).toLocaleString('vi-VN')} đ` : "Liên hệ";

      const getSelectedOptions = () => {
        const selected = {};
        attributeNames.forEach(name => {
          const checkedInput = variantsContainer.querySelector(`input[name="attribute_${name}"]:checked`);
          if (checkedInput) selected[name] = checkedInput.value;
        });
        return selected;
      };

      const findMatchingVariant = (options) => {
        if (Object.keys(options).length < attributeNames.length) return null;
        return variants.find(variant => attributeNames.every(name => variant.attributes[name] === options[name]));
      };

      const updateAvailability = () => {
        const currentSelections = getSelectedOptions();
        allInputs.forEach(input => {
          const attrName = input.dataset.attributeName;
          const attrValue = input.value;
          const testSelections = { ...currentSelections, [attrName]: attrValue };
          const isAvailable = variants.some(variant => Object.entries(testSelections).every(([key, value]) => variant.attributes[key] === value));
          input.parentElement.classList.toggle('is-unavailable', !isAvailable);
        });
      };

      const updateDisplay = () => {
        const selections = getSelectedOptions();
        const matchedVariant = findMatchingVariant(selections);
        
        if (matchedVariant) {
          priceDisplay.textContent = formatPrice(matchedVariant.price);
          const newImageSrc = matchedVariant.image_url ? `${window.BASE_URL || ''}/${matchedVariant.image_url}` : originalImageSrc;
          if (mainImage.src !== newImageSrc) {
            mainImage.src = newImageSrc;
            const galleryThumbnails = document.querySelectorAll('.thumbnail-item-v2');
            galleryThumbnails.forEach(t => t.classList.toggle('active', t.dataset.imageSrc === newImageSrc));
          }
        } else {
          priceDisplay.textContent = "Chọn phiên bản để xem giá";
        }
        updateAvailability();
      };

      variantsContainer.addEventListener('change', (e) => {
        const clickedInput = e.target;
        if (!clickedInput.matches('input[type="radio"]')) return;

        let currentSelections = getSelectedOptions();
        let matchedVariant = findMatchingVariant(currentSelections);

        if (!matchedVariant) {
          const lastClickedAttr = clickedInput.dataset.attributeName;
          const lastClickedValue = clickedInput.value;
          const possibleVariants = variants.filter(v => v.attributes[lastClickedAttr] === lastClickedValue);

          if (possibleVariants.length > 0) {
            const correctionVariant = possibleVariants[0];
            Object.entries(correctionVariant.attributes).forEach(([attr, val]) => {
              const inputToSelect = variantsContainer.querySelector(`input[data-attribute-name="${attr}"][value="${val}"]`);
              if (inputToSelect) inputToSelect.checked = true;
            });
          }
        }
        updateDisplay();
      });

      updateDisplay(); // Initial run
    }

    // --- 3. LOGIC GALLERY, TABS, FB BUTTON ---
    if (mainImage) {
      const galleryV2 = page.querySelector(".product-gallery-v2");
      if (galleryV2) {
        const thumbnails = galleryV2.querySelectorAll(".thumbnail-item-v2");
        if (thumbnails.length > 0) {
          thumbnails.forEach(thumb => {
            thumb.addEventListener("click", function() {
              const newImageSrc = this.dataset.imageSrc;
              if (newImageSrc && mainImage.src !== newImageSrc) {
                mainImage.src = newImageSrc;
                thumbnails.forEach(t => t.classList.remove("active"));
                this.classList.add("active");
              }
            });
          });
        }
      }
    }
    if (productTabsContainer) {
      const tabLinks = productTabsContainer.querySelectorAll(".tab-link");
      const tabPanes = productTabsContainer.querySelectorAll(".tab-pane");
      if (tabLinks.length > 0 && tabPanes.length > 0) {
        tabLinks.forEach(link => {
          link.addEventListener("click", () => {
            const tabId = link.dataset.tab;
            tabLinks.forEach(l => l.classList.remove("active"));
            link.classList.add("active");
            tabPanes.forEach(pane => pane.classList.toggle("active", pane.id === tabId));
          });
        });
      }
    }
    if (fbButton) {
      fbButton.addEventListener("click", function (e) {
        e.preventDefault();
        const productUrl = window.location.href;
        const originalText = this.querySelector("span").innerText;
        const textToCopy = `Chào bạn, tôi quan tâm đến sản phẩm này: ${productUrl}`;
        navigator.clipboard.writeText(textToCopy).then(() => {
          this.querySelector("span").innerText = "Đã sao chép!";
          window.open(this.href, "_blank");
          setTimeout(() => { this.querySelector("span").innerText = originalText; }, 2500);
        }).catch(() => { window.open(this.href, "_blank"); });
      });
    }
  }

  function initializeScrollAnimations() {
    const animatedElements = document.querySelectorAll("[data-animate]");
    if (animatedElements.length > 0) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });
      animatedElements.forEach((element) => observer.observe(element));
    }
  }

  function initializeMapTabs() {
    const tabsContainer = document.querySelector('.map-tabs');
    if (!tabsContainer) return;
    tabsContainer.addEventListener('click', function (e) {
      if (e.target.classList.contains('map-tab-btn')) {
        const mapId = e.target.dataset.map;
        if (!mapId) return;
        tabsContainer.querySelectorAll('.map-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.map-pane').forEach(pane => pane.classList.remove('active'));
        e.target.classList.add('active');
        const activePane = document.getElementById(mapId);
        if (activePane) activePane.classList.add('active');
      }
    });
  }
});
