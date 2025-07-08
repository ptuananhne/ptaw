document.addEventListener("DOMContentLoaded", function () {
  const baseUrl = window.BASE_URL || "";

  // --- LOGIC CHO TRANG DANH SÁCH SẢN PHẨM (INDEX) ---
  const indexPageContainer = document.querySelector(".product-table");
  if (indexPageContainer) {
    initializeIndexPage();
  }

  // --- LOGIC CHO TRANG FORM THÊM/SỬA SẢN PHẨM ---
  const formPageContainer = document.getElementById("productForm");
  if (formPageContainer) {
    initializeFormPage();
  }

  function initializeIndexPage() {
    const searchInput = document.getElementById("searchInput");
    const categoryFilter = document.getElementById("categoryFilter");
    const brandFilter = document.getElementById("brandFilter");
    const pagination = document.querySelector(".pagination");
    const tableBody = document.getElementById("product-table-body");

    function debounce(func, wait) {
      let timeout;
      return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
      };
    }

    function performSearch() {
      const query = searchInput.value;
      const categoryId = categoryFilter.value;
      const brandId = brandFilter.value;
      const params = new URLSearchParams();
      if (query) params.append("q", query);
      if (categoryId) params.append("category", categoryId);
      if (brandId) params.append("brand", brandId);

      const searchUrl = `${baseUrl}/admin/products/search?${params.toString()}`;
      if (pagination) {
        pagination.style.display =
          query || categoryId || brandId ? "none" : "flex";
      }

      fetch(searchUrl)
        .then((response) => response.json())
        .then((data) => renderProductTable(data))
        .catch((error) => console.error("Lỗi khi lọc sản phẩm:", error));
    }

    function updateBrandFilter() {
      performSearch();
      const categoryId = categoryFilter.value;
      brandFilter.innerHTML = '<option value="">Tất cả thương hiệu</option>';
      brandFilter.disabled = true;
      if (!categoryId) {
        brandFilter.disabled = false;
        return;
      }
      fetch(`${baseUrl}/admin/products/getBrandsByCategory/${categoryId}`)
        .then((response) => response.json())
        .then((brands) => {
          brands.forEach((brand) =>
            brandFilter.add(new Option(brand.name, brand.id))
          );
          brandFilter.disabled = false;
        })
        .catch((error) => console.error("Lỗi khi lấy thương hiệu:", error));
    }

    searchInput.addEventListener("keyup", debounce(performSearch, 350));
    categoryFilter.addEventListener("change", updateBrandFilter);
    brandFilter.addEventListener("change", performSearch);
  }

  function initializeFormPage() {
    const nameInput = document.getElementById("name");
    const slugInput = document.getElementById("slug");
    if (nameInput && slugInput) {
      nameInput.addEventListener("keyup", () => {
        slugInput.value = generateSlug(nameInput.value);
      });
    }

    const galleryInput = document.getElementById("gallery_images_input");
    const galleryContainer = document.getElementById("gallery-container");
    if (galleryInput && galleryContainer) {
      galleryInput.addEventListener("change", function (event) {
        for (const file of event.target.files) {
          const reader = new FileReader();
          reader.onload = function (e) {
            const newItem = document.createElement("div");
            newItem.className = "gallery-item is-new";
            newItem.innerHTML = `<img src="${e.target.result}" alt="New Image"><div class="gallery-item-actions"><span>Mới</span></div>`;
            galleryContainer.appendChild(newItem);
          };
          reader.readAsDataURL(file);
        }
      });
    }
  }
});

// === CÁC HÀM TOÀN CỤC ===

function renderProductTable(products) {
  const tableBody = document.getElementById("product-table-body");
  const baseUrl = window.BASE_URL || "";
  tableBody.innerHTML = "";

  if (!products || products.length === 0) {
    tableBody.innerHTML =
      '<tr><td colspan="6" style="text-align: center; padding: 2rem;">Không tìm thấy sản phẩm nào.</td></tr>';
    return;
  }

  products.forEach((product) => {
    const priceFormatted = new Intl.NumberFormat("vi-VN").format(product.price);
    const row = `
            <tr>
                <td><img src="${baseUrl}/uploads/${escapeHTML(
      product.image_url
    )}" class="product-thumbnail" onerror="this.src='https://placehold.co/60x60/e2e8f0/475569?text=N/A'"></td>
                <td class="product-name">${escapeHTML(product.name)}</td>
                <td>${escapeHTML(product.category_name)}</td>
                <td>${priceFormatted}đ</td>
                <td>${escapeHTML(product.view_count)}</td>
                <td>
                    <a href="${baseUrl}/admin/products/edit/${
      product.id
    }" class="btn btn-sm btn-warning">Sửa</a>
                    <button class="btn btn-sm btn-danger" onclick="showDeleteModal(${
                      product.id
                    }, '${addSlashes(escapeHTML(product.name))}')">Xóa</button>
                </td>
            </tr>`;
    tableBody.innerHTML += row;
  });
}

function generateSlug(text) {
  return text
    .toString()
    .toLowerCase()
    .replace(/\s+/g, "-")
    .replace(/[^\u00C0-\u1EF9\w\-]+/g, "")
    .replace(/\-\-+/g, "-")
    .replace(/^-+/, "")
    .replace(/-+$/, "");
}

function deleteGalleryImage(button, imageId) {
  if (confirm("Bạn có chắc chắn muốn xóa ảnh này?")) {
    const form = document.getElementById("productForm");
    const hiddenInput = document.createElement("input");
    hiddenInput.type = "hidden";
    hiddenInput.name = "images_to_delete[]";
    hiddenInput.value = imageId;
    form.appendChild(hiddenInput);
    button.closest(".gallery-item").style.display = "none";
  }
}

function setAsMainImage(button, imageUrl) {
  document.getElementById("mainImagePreview").src = button
    .closest(".gallery-item")
    .querySelector("img").src;
  document.getElementById("new_main_image_url").value = imageUrl;
  document
    .querySelectorAll(".gallery-item")
    .forEach((item) => item.classList.remove("is-main"));
  button.closest(".gallery-item").classList.add("is-main");
}

function showDeleteModal(id, name) {
  const modal = document.getElementById("deleteModal");
  if (modal) {
    document.getElementById("productIdToDelete").value = id;
    document.getElementById("productName").innerText = name;
    modal.__x.data.open = true;
  }
}

function escapeHTML(str) {
  if (str === null || str === undefined) return "";
  return str.toString().replace(
    /[&<>"']/g,
    (m) =>
      ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;",
      }[m])
  );
}
function addSlashes(str) {
  return (str + "").replace(/[\\"']/g, "\\$&").replace(/\u0000/g, "\\0");
}
