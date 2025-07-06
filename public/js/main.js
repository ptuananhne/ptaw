document.addEventListener("DOMContentLoaded", function () {
  // ... code menu mobile giữ nguyên ...
  const menuToggle = document.getElementById("mobile-menu-toggle");
  const sidebar = document.getElementById("sidebar");
  if (menuToggle && sidebar) {
    menuToggle.addEventListener("click", function () {
      sidebar.classList.toggle("is-open");
    });
  }

  // --- CODE SLIDER ĐƯỢC NÂNG CẤP ---
  function initializeSlider(sliderElement) {
    const trackContainer = sliderElement.querySelector(
      ".slider-track-container"
    );
    const track = sliderElement.querySelector(".slider-track");
    const prevBtn = sliderElement.querySelector(".slider-btn.prev");
    const nextBtn = sliderElement.querySelector(".slider-btn.next");

    if (!track || !prevBtn || !nextBtn || !trackContainer) return;

    // Hàm cập nhật trạng thái của nút
    const updateButtons = () => {
      const scrollLeft = track.scrollLeft;
      const scrollWidth = track.scrollWidth;
      const clientWidth = track.clientWidth;

      // Vô hiệu hóa nút "prev" nếu đang ở đầu
      prevBtn.disabled = scrollLeft <= 0;

      // Vô hiệu hóa nút "next" nếu đã cuộn đến cuối
      // Thêm một khoảng đệm nhỏ (1px) để xử lý sai số làm tròn
      nextBtn.disabled = scrollLeft + clientWidth >= scrollWidth - 1;
    };

    // Di chuyển đến slide tiếp theo
    nextBtn.addEventListener("click", () => {
      // Cuộn một khoảng bằng chiều rộng của phần hiển thị
      track.scrollBy({ left: trackContainer.clientWidth, behavior: "smooth" });
    });

    // Di chuyển về slide trước đó
    prevBtn.addEventListener("click", () => {
      track.scrollBy({ left: -trackContainer.clientWidth, behavior: "smooth" });
    });

    // Cập nhật trạng thái nút khi cuộn (kể cả khi người dùng tự kéo trên di động)
    track.addEventListener("scroll", updateButtons);

    // Cập nhật khi kích thước cửa sổ thay đổi
    // Sử dụng debounce để tránh gọi hàm liên tục, tối ưu hiệu năng
    let resizeTimer;
    window.addEventListener("resize", () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(updateButtons, 250);
    });

    // Cập nhật trạng thái nút lần đầu tiên khi tải trang
    // Dùng setTimeout nhỏ để đảm bảo layout đã được render hoàn chỉnh
    setTimeout(updateButtons, 100);
  }

  // Áp dụng cho tất cả các slider trên trang
  const allSliders = document.querySelectorAll(".product-slider");
  allSliders.forEach((slider) => {
    initializeSlider(slider);
  });
});
