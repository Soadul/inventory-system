<?php
$isEdit = isset($product);
$actionUrl = $isEdit ? $this->getUrl("products/edit/{$product['id']}") : $this->getUrl('products/create');
?>

<div class="card-panel" style="max-width: 700px; margin: 0 auto;">
    <div class="card-title">
        <span><?= $isEdit ? '✏️ Edit Product Specifications' : '➕ Register New Product in Catalog' ?></span>
        <a href="<?= $this->getUrl('products') ?>" class="btn btn-secondary btn-sm">Cancel</a>
    </div>

    <form action="<?= $actionUrl ?>" method="POST">
        <div class="form-group">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Soyabean Oil 1L" value="<?= $isEdit ? htmlspecialchars($product['name']) : '' ?>" required>
        </div>

        <div class="sales-row" style="grid-template-columns: 1fr 1fr; background:none; border:none; padding:0; margin-bottom:0; gap:20px;">
            <div class="form-group">
                <label for="sku">SKU Code *</label>
                <input type="text" id="sku" name="sku" class="form-control" placeholder="e.g. GR-OIL-01" value="<?= $isEdit ? htmlspecialchars($product['sku']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category_id">Product Category *</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="" disabled selected>-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($isEdit && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="sales-row" style="grid-template-columns: 1fr 1fr; background:none; border:none; padding:0; margin-bottom:0; gap:20px;">
            <div class="form-group">
                <label for="cost">Purchasing Cost (TK) *</label>
                <input type="number" id="cost" name="cost" step="0.01" min="0" class="form-control" placeholder="0.00" value="<?= $isEdit ? htmlspecialchars($product['cost']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="price">Selling Price (TK) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" class="form-control" placeholder="0.00" value="<?= $isEdit ? htmlspecialchars($product['price']) : '' ?>" required>
            </div>
        </div>

        <div class="sales-row" style="grid-template-columns: 1fr 1fr; background:none; border:none; padding:0; margin-bottom:0; gap:20px;">
            <div class="form-group">
                <label for="stock_quantity">Initial Stock Quantity</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0" class="form-control" placeholder="0" value="<?= $isEdit ? htmlspecialchars($product['stock_quantity']) : '0' ?>" <?= $isEdit ? 'disabled' : '' ?>>
                <?php if ($isEdit): ?>
                    <small style="color:var(--text-muted); display:block; margin-top:4px;">* Stock quantities are edited by executing a Restock log transaction.</small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="min_stock_alert">Minimum Low Stock Alert Level</label>
                <input type="number" id="min_stock_alert" name="min_stock_alert" min="0" class="form-control" placeholder="5" value="<?= $isEdit ? htmlspecialchars($product['min_stock_alert']) : '5' ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Item Description</label>
            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief technical descriptions..."><?= $isEdit ? htmlspecialchars($product['description']) : '' ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
            <span><?= $isEdit ? 'Save Specifications Updates' : 'Add Product to Inventory' ?></span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
        </button>
    </form>
</div>
