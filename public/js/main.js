document.addEventListener("DOMContentLoaded", function () {
  const menuButton = document.getElementById("mobile-menu-button");
  const navMenu = document.getElementById("main-nav");

  if (menuButton && navMenu) {
    menuButton.addEventListener("click", function () {
      // Thêm/xóa class 'is-open' để hiển thị/ẩn menu
      navMenu.classList.toggle("is-open");

      // Cập nhật thuộc tính aria-expanded cho accessbility
      const isExpanded = navMenu.classList.contains("is-open");
      this.setAttribute("aria-expanded", isExpanded);
    });
  }
});
