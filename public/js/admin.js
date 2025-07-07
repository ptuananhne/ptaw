// public/js/admin.js

document.addEventListener("DOMContentLoaded", function () {
  // --- Chức năng 1: Lọc thương hiệu theo danh mục ---
  const categoryFilter = document.getElementById("category-filter");
  const brandFilter = document.getElementById("brand-filter");

  if (categoryFilter && brandFilter) {
    categoryFilter.addEventListener("change", function () {
      const categoryId = this.value;
      brandFilter.disabled = true;
      brandFilter.innerHTML = '<option value="">Đang tải...</option>';

      const apiUrl = `${window.APP_BASE_URL}/admin.php?url=api/getBrandsByCategory/${categoryId}`;

      fetch(apiUrl)
        .then((response) => {
          if (!response.ok) {
            return response.text().then((text) => {
              throw new Error(text || "Lỗi Server");
            });
          }
          return response.json();
        })
        .then((brands) => {
          brandFilter.innerHTML =
            '<option value="">Tất cả thương hiệu</option>';
          brands.forEach((brand) => {
            const option = document.createElement("option");
            option.value = brand.id;
            option.textContent = brand.name;
            brandFilter.appendChild(option);
          });
          brandFilter.disabled = false;
        })
        .catch((error) => {
          console.error("Lỗi khi lấy danh sách thương hiệu:", error);
          brandFilter.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
        });
    });
  }

  // --- Chức năng 2 & 3: Quản lý ảnh trong form sản phẩm ---
  const imageGrid = document.getElementById("image-selection-grid");

  if (imageGrid) {
    // Hàm để cập nhật giao diện khi chọn ảnh đại diện
    function updateFeaturedImageUI() {
      const allCards = imageGrid.querySelectorAll(".image-card");
      allCards.forEach((card) => {
        const radio = card.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
          card.classList.add("is-featured");
        } else {
          card.classList.remove("is-featured");
        }
      });
    }

    // Lắng nghe sự kiện click trên toàn bộ grid
    imageGrid.addEventListener("click", function (e) {
      // --- Xử lý nút xóa ảnh ---
      if (e.target && e.target.classList.contains("btn-delete-gallery")) {
        e.preventDefault();
        e.stopPropagation(); // Ngăn sự kiện click lan ra card

        const button = e.target;
        const imageId = button.dataset.imageId;

        if (
          confirm(
            "Bạn có chắc chắn muốn xóa ảnh này? Thao tác này không thể hoàn tác."
          )
        ) {
          const apiUrl = `${window.APP_BASE_URL}/admin.php?url=adminProduct/deleteGalleryImage/${imageId}`;

          fetch(apiUrl)
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                // Xóa element ảnh khỏi giao diện
                button.closest(".image-card").remove();
              } else {
                alert("Lỗi: " + (data.message || "Không thể xóa ảnh."));
              }
            })
            .catch((error) => {
              console.error("Lỗi khi xóa ảnh:", error);
              alert("Đã có lỗi xảy ra khi thực hiện yêu cầu.");
            });
        }
        return; // Dừng xử lý tại đây
      }

      // --- Xử lý chọn ảnh đại diện ---
      const card = e.target.closest(".image-card");
      if (card) {
        const radio = card.querySelector('input[type="radio"]');
        if (radio && !radio.checked) {
          radio.checked = true;
          // Gọi hàm cập nhật UI
          updateFeaturedImageUI();
        }
      }
    });

    // Cập nhật UI lần đầu khi tải trang
    updateFeaturedImageUI();
  }
});
