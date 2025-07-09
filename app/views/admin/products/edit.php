<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .ts-control { padding: 0.5rem 0.75rem !important; border-radius: 0.375rem !important; border: 1px solid #d1d5db !important; }
        .spec-row:not(:first-child) .remove-spec,
        .attribute-item:not(:first-child) .remove-attribute-btn { 
            display: inline-flex; 
        }
        .remove-spec, .remove-attribute-btn { display: none; }
    </style>
</head>

<body class="bg-gray-100">
    <?php
        $product = $data['product'];
        $errors = $data['errors'] ?? [];
        $productType = $product->product_type ?? 'simple';
        $specs = !empty($product->specifications) ? json_decode($product->specifications, true) : [];
        $all_attributes = $data['all_attributes'] ?? [];
        $selected_attributes_data = !empty($product->attributes) ? json_decode($product->attributes, true) : [];
        $variants = $product->variants ?? [];
    ?>
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 min-h-screen bg-gray-800 text-white p-4">
            <h2 class="text-2xl font-bold mb-6">Admin</h2>
            <nav>
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="block py-2.5 px-4 rounded hover:bg-gray-700">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/product" class="block py-2.5 px-4 rounded bg-gray-700">Sản phẩm</a>
                <a href="<?php echo BASE_URL; ?>/admin/productAttribute" class="block py-2.5 px-4 rounded hover:bg-gray-700">Thuộc tính</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($data['title']); ?></h1>
                <a href="<?php echo BASE_URL; ?>/admin/product" class="text-blue-500 hover:text-blue-700">&larr; Quay lại danh sách</a>
            </div>
            <?php flash('product_message'); ?>

            <form id="product-form" action="<?php echo BASE_URL; ?>/admin/product/edit/<?php echo $product->id; ?>" method="POST" enctype="multipart/form-data">
                <?php if (!empty($errors['form'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <strong class="font-bold">Đã có lỗi xảy ra!</strong>
                        <span class="block sm:inline"><?php echo $errors['form']; ?></span>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-3 gap-8">
                    <!-- Cột trái: Thông tin chính -->
                    <div class="col-span-2 space-y-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Thông tin cơ bản</h3>
                            <div class="mb-4">
                                <label for="name" class="block text-gray-700 font-bold mb-2">Tên sản phẩm:</label>
                                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product->name ?? ''); ?>" class="shadow-sm border rounded w-full py-2 px-3">
                            </div>
                            <div class="mb-4">
                                <label for="description" class="block text-gray-700 font-bold mb-2">Mô tả:</label>
                                <textarea name="description" id="description" rows="5" class="shadow-sm border rounded w-full py-2 px-3"><?php echo htmlspecialchars($product->description ?? ''); ?></textarea>
                            </div>
                            <div class="mb-4">
                                <h4 class="text-lg font-semibold mb-2">Thông số kỹ thuật</h4>
                                <div id="specifications-container" class="space-y-2">
                                    <?php if (!empty($specs)): foreach ($specs as $key => $value): ?>
                                        <div class="flex items-center gap-2 spec-row">
                                            <input type="text" name="spec_key[]" placeholder="Tên thông số" value="<?php echo htmlspecialchars($key); ?>" class="flex-1 shadow-sm border rounded px-3 py-2">
                                            <input type="text" name="spec_value[]" placeholder="Giá trị" value="<?php echo htmlspecialchars($value); ?>" class="flex-1 shadow-sm border rounded px-3 py-2">
                                            <button type="button" class="remove-spec items-center justify-center bg-red-500 text-white w-10 h-10 rounded">-</button>
                                        </div>
                                    <?php endforeach; else: ?>
                                        <div class="flex items-center gap-2 spec-row">
                                            <input type="text" name="spec_key[]" class="flex-1 shadow-sm border rounded px-3 py-2">
                                            <input type="text" name="spec_value[]" class="flex-1 shadow-sm border rounded px-3 py-2">
                                            <button type="button" class="remove-spec items-center justify-center bg-red-500 text-white w-10 h-10 rounded">-</button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" id="add-spec" class="mt-2 text-sm text-blue-600 hover:underline">+ Thêm thông số</button>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Dữ liệu sản phẩm</h3>
                            <div class="mb-4">
                                <select name="product_type" id="product_type" class="w-full p-2 border rounded">
                                    <option value="simple" <?php echo ($productType == 'simple') ? 'selected' : ''; ?>>Sản phẩm đơn giản</option>
                                    <option value="variable" <?php echo ($productType == 'variable') ? 'selected' : ''; ?>>Sản phẩm có biến thể</option>
                                </select>
                            </div>

                            <div id="simple-product-data" class="<?php echo ($productType == 'simple') ? '' : 'hidden'; ?>">
                                <label for="price" class="block text-gray-700 font-bold mb-2">Giá:</label>
                                <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($product->price ?? ''); ?>" class="shadow-sm border rounded w-full py-2 px-3">
                            </div>

                            <div id="variable-product-data" class="<?php echo ($productType == 'variable') ? '' : 'hidden'; ?> space-y-6">
                                <div class="p-4 border rounded-md">
                                    <label for="attribute-selector" class="block font-medium text-gray-700 mb-2">Chọn thuộc tính cho sản phẩm</label>
                                    <div class="flex items-center gap-4">
                                        <select id="attribute-selector" class="flex-grow">
                                            <option value="">-- Chọn hoặc thêm thuộc tính mới --</option>
                                            <?php foreach($all_attributes as $attr): ?>
                                                <option value="<?php echo $attr->id; ?>"><?php echo htmlspecialchars($attr->name); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" id="add-attribute-btn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">Thêm</button>
                                    </div>
                                </div>
                                
                                <div id="attributes-container" class="space-y-4">
                                    <?php foreach($selected_attributes_data as $selected_attr): ?>
                                        <?php 
                                            $full_attr_data = null;
                                            foreach($all_attributes as $attr) {
                                                if ($attr->id == $selected_attr['id']) {
                                                    $full_attr_data = $attr;
                                                    break;
                                                }
                                            }
                                            if (!$full_attr_data) continue;
                                            
                                            $existing_terms = array_column($full_attr_data->terms, 'name');
                                            $all_possible_terms = array_unique(array_merge($existing_terms, $selected_attr['values']));
                                        ?>
                                        <div class="attribute-item border p-4 rounded-md" data-id="<?php echo $full_attr_data->id; ?>">
                                            <input type="hidden" name="product_attributes[<?php echo $full_attr_data->id; ?>][name]" value="<?php echo htmlspecialchars($full_attr_data->name); ?>">
                                            <div class="flex justify-between items-center mb-2">
                                                <label class="font-bold text-gray-800"><?php echo htmlspecialchars($full_attr_data->name); ?></label>
                                                <button type="button" class="remove-attribute-btn text-red-500 hover:underline text-sm">Xóa</button>
                                            </div>
                                            <select name="product_attributes[<?php echo $full_attr_data->id; ?>][values][]" multiple>
                                                <?php foreach($all_possible_terms as $term_name): ?>
                                                    <option value="<?php echo htmlspecialchars($term_name); ?>" <?php echo in_array($term_name, $selected_attr['values']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($term_name); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div id="variants-section" class="hidden">
                                    <div class="flex justify-between items-center mb-2 border-t pt-4">
                                        <label class="block text-gray-700 font-bold">Các biến thể:</label>
                                        <button type="button" id="add-variant-btn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">+ Thêm biến thể</button>
                                    </div>
                                    <div id="variants-container" class="space-y-4">
                                        <?php foreach($variants as $index => $variant): 
                                            $variant_attrs = json_decode($variant->attributes, true);
                                        ?>
                                            <div class="variant-item p-4 border rounded-md bg-white" data-variant-index="<?php echo $index; ?>">
                                                <input type="hidden" name="variants[<?php echo $index; ?>][attributes]" value='<?php echo htmlspecialchars($variant->attributes, ENT_QUOTES, 'UTF-8'); ?>' class="variant-attributes-json">
                                                <div class="flex items-center gap-4">
                                                    <?php foreach($selected_attributes_data as $attr): ?>
                                                        <div class="flex-grow">
                                                            <label class="text-sm font-medium text-gray-600"><?php echo htmlspecialchars($attr['name']); ?></label>
                                                            <select class="variant-attribute-select w-full p-2 border rounded mt-1" data-attribute-name="<?php echo htmlspecialchars($attr['name']); ?>">
                                                                <?php foreach($attr['values'] as $val): ?>
                                                                    <option value="<?php echo htmlspecialchars($val); ?>" <?php echo (isset($variant_attrs[$attr['name']]) && $variant_attrs[$attr['name']] == $val) ? 'selected' : ''; ?>>
                                                                        <?php echo htmlspecialchars($val); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <div class="flex-grow">
                                                        <label class="text-sm font-medium text-gray-600">Giá</label>
                                                        <input type="number" name="variants[<?php echo $index; ?>][price]" value="<?php echo htmlspecialchars($variant->price); ?>" placeholder="Giá" class="w-full p-2 border rounded mt-1">
                                                    </div>
                                                    <button type="button" class="remove-variant-btn self-end bg-red-500 text-white p-2 rounded h-10">-</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-1 space-y-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Phân loại</h3>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-bold mb-2">Danh mục (Không thể thay đổi):</label>
                                <input type="text" value="<?php echo htmlspecialchars($data['categories'][array_search($product->category_id, array_column($data['categories'], 'id'))]->name ?? 'N/A'); ?>" class="shadow-sm border rounded w-full py-2 px-3 text-gray-500 bg-gray-200" disabled>
                            </div>
                            <div>
                                <label for="brand_id" class="block text-gray-700 font-bold mb-2">Thương hiệu:</label>
                                <select name="brand_id" id="brand_id" class="w-full p-2 border rounded">
                                    <?php foreach ($data['brands'] as $brand): ?>
                                        <option value="<?php echo $brand->id; ?>" <?php echo ($product->brand_id == $brand->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($brand->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Ảnh sản phẩm</h3>
                            <?php if(!empty($product->image_url)): ?>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-bold mb-2">Ảnh đại diện hiện tại:</label>
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($product->image_url); ?>" alt="Ảnh đại diện" class="w-full h-48 object-contain rounded border p-1">
                            </div>
                            <?php endif; ?>
                            <label for="image" class="block text-gray-700 font-bold mb-2">Thay ảnh đại diện mới:</label>
                            <input type="file" name="image" id="image" class="w-full">
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex items-center justify-end">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const allAttributes = <?php echo json_encode($all_attributes, JSON_UNESCAPED_UNICODE); ?>;
    
    const productTypeSelect = document.getElementById('product_type');
    const simpleProductData = document.getElementById('simple-product-data');
    const variableProductData = document.getElementById('variable-product-data');
    const attributesContainer = document.getElementById('attributes-container');
    const variantsSection = document.getElementById('variants-section');
    const addVariantBtn = document.getElementById('add-variant-btn');
    const variantsContainer = document.getElementById('variants-container');
    
    let variantIndex = <?php echo count($variants); ?>;

    function initializeTomSelect(element, canCreate) {
        if (element.tomselect) return;
        new TomSelect(element, {
            plugins: ['remove_button'],
            create: canCreate,
            onItemAdd: function() {
                this.setTextboxValue('');
                this.refreshOptions();
            }
        });
    }

    document.querySelectorAll('#attributes-container select').forEach(el => initializeTomSelect(el, true));
    if(document.querySelectorAll('#attributes-container .attribute-item').length > 0) {
        variantsSection.classList.remove('hidden');
    }

    const mainAttributeSelector = new TomSelect('#attribute-selector', { create: true });

    productTypeSelect.addEventListener('change', function () {
        const isVariable = this.value === 'variable';
        simpleProductData.classList.toggle('hidden', isVariable);
        variableProductData.classList.toggle('hidden', !isVariable);
    });
    
    document.getElementById('add-attribute-btn').addEventListener('click', function() {
        const selectedValue = mainAttributeSelector.getValue();
        if (!selectedValue) return;

        const isNewAttribute = !allAttributes.some(attr => attr.id == selectedValue);
        let attributeData;

        if (isNewAttribute) {
            const newAttributeName = mainAttributeSelector.options[selectedValue].text;
            attributeData = { id: `new_${newAttributeName}`, name: newAttributeName, terms: [] };
        } else {
            if (document.querySelector(`.attribute-item[data-id="${selectedValue}"]`)) {
                alert('Thuộc tính này đã được thêm.');
                return;
            }
            attributeData = allAttributes.find(attr => attr.id == selectedValue);
        }
        
        if (!attributeData) return;

        const attributeHtml = `
            <div class="attribute-item border p-4 rounded-md" data-id="${attributeData.id}">
                <input type="hidden" name="product_attributes[${attributeData.id}][name]" value="${attributeData.name}">
                <div class="flex justify-between items-center mb-2">
                    <label class="font-bold text-gray-800">${attributeData.name}</label>
                    <button type="button" class="remove-attribute-btn text-red-500 hover:underline text-sm">Xóa</button>
                </div>
                <select name="product_attributes[${attributeData.id}][values][]" multiple>
                    ${attributeData.terms.map(term => `<option value="${term.name}">${term.name}</option>`).join('')}
                </select>
            </div>
        `;
        attributesContainer.insertAdjacentHTML('beforeend', attributeHtml);
        
        const newSelect = attributesContainer.querySelector(`.attribute-item[data-id="${attributeData.id}"] select`);
        initializeTomSelect(newSelect, true);

        variantsSection.classList.remove('hidden');
        mainAttributeSelector.clear();
    });

    attributesContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-attribute-btn')) {
            e.target.closest('.attribute-item').remove();
            if (attributesContainer.children.length === 0) {
                variantsSection.classList.add('hidden');
            }
        }
    });

    addVariantBtn.addEventListener('click', function() {
        const selectedAttributes = [];
        document.querySelectorAll('.attribute-item').forEach(item => {
            const id = item.dataset.id;
            const name = item.querySelector('input[type=hidden]').value;
            const selectEl = item.querySelector('select');
            if (selectEl && selectEl.tomselect) {
                const values = selectEl.tomselect.getValue();
                if (values.length > 0) {
                    selectedAttributes.push({ id, name, values });
                }
            }
        });

        if (selectedAttributes.length === 0) {
            alert('Vui lòng chọn ít nhất một giá trị cho một thuộc tính.');
            return;
        }

        let selectorsHtml = selectedAttributes.map(attr => `
            <div class="flex-grow">
                <label class="text-sm font-medium text-gray-600">${attr.name}</label>
                <select class="variant-attribute-select w-full p-2 border rounded mt-1" data-attribute-name="${attr.name}">
                    ${attr.values.map(val => `<option value="${val}">${val}</option>`).join('')}
                </select>
            </div>`).join('');

        const variantHtml = `
            <div class="variant-item p-4 border rounded-md bg-white" data-variant-index="${variantIndex}">
                <input type="hidden" name="variants[${variantIndex}][attributes]" class="variant-attributes-json">
                <div class="flex items-center gap-4">
                    ${selectorsHtml}
                    <div class="flex-grow">
                        <label class="text-sm font-medium text-gray-600">Giá</label>
                        <input type="number" name="variants[${variantIndex}][price]" placeholder="Giá" class="w-full p-2 border rounded mt-1">
                    </div>
                    <button type="button" class="remove-variant-btn self-end bg-red-500 text-white p-2 rounded h-10">-</button>
                </div>
            </div>`;
        variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
        updateVariantJson(variantIndex);
        variantIndex++;
    });

    variantsContainer.addEventListener('change', (e) => {
        if (e.target.classList.contains('variant-attribute-select')) {
            const variantItem = e.target.closest('.variant-item');
            updateVariantJson(variantItem.dataset.variantIndex);
        }
    });

    variantsContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-variant-btn')) {
            e.target.closest('.variant-item').remove();
        }
    });

    function updateVariantJson(index) {
        const variantItem = variantsContainer.querySelector(`.variant-item[data-variant-index="${index}"]`);
        if (!variantItem) return;
        const attributes = {};
        variantItem.querySelectorAll('.variant-attribute-select').forEach(select => {
            attributes[select.dataset.attributeName] = select.value;
        });
        variantItem.querySelector('.variant-attributes-json').value = JSON.stringify(attributes);
    }
    
    const specContainer = document.getElementById('specifications-container');
    document.getElementById('add-spec').addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'flex items-center gap-2 spec-row';
        newRow.innerHTML = `
            <input type="text" name="spec_key[]" placeholder="Tên thông số" class="flex-1 shadow-sm border rounded px-3 py-2">
            <input type="text" name="spec_value[]" placeholder="Giá trị" class="flex-1 shadow-sm border rounded px-3 py-2">
            <button type="button" class="remove-spec items-center justify-center bg-red-500 text-white w-10 h-10 rounded">-</button>
        `;
        specContainer.appendChild(newRow);
    });
    specContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-spec')) {
            e.target.closest('.spec-row').remove();
        }
    });
});
</script>
</body>
</html>
