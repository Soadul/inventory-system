<div class="card-panel" style="max-width: 600px; margin: 0 auto;">
    <div class="card-title">
        <span>🗑️ Record Stock Damage & Waste Log</span>
        <a href="<?= $this->getUrl('damages') ?>" class="btn btn-secondary btn-sm">Cancel</a>
    </div>

    <form action="<?= $this->getUrl('damages/create') ?>" method="POST">
        
        <div class="form-group">
            <label for="product_id">Select Product *</label>
            <select id="product_id" name="product_id" class="form-control" required autofocus>
                <option value="" disabled selected>-- Select Product --</option>
                <?php foreach ($products as $prod): ?>
                    <option value="<?= $prod['id'] ?>" data-stock="<?= $prod['stock_quantity'] ?>">
                        <?= htmlspecialchars($prod['name']) ?> (SKU: <?= htmlspecialchars($prod['sku']) ?> | Current Stock: <?= $prod['stock_quantity'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="sales-row" style="grid-template-columns: 1.2fr 0.8fr; background:none; border:none; padding:0; margin-bottom:0; gap:20px;">
            <div class="form-group">
                <label for="reason">Primary Waste Reason *</label>
                <select id="reason" name="reason" class="form-control" required>
                    <option value="" disabled selected>-- Select Reason --</option>
                    <option value="Broken">Broken (Physical damage)</option>
                    <option value="Expired">Expired (Shelf life exceeded)</option>
                    <option value="Lost">Lost (Shortage during audit)</option>
                    <option value="Spoiled">Spoiled (Contaminated / Unfit)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">Damaged Quantity *</label>
                <input type="number" id="quantity" name="quantity" min="1" class="form-control" placeholder="Qty" required>
                <small id="stock-helper-text" style="color:var(--text-muted); display:block; margin-top:4px;"></small>
            </div>
        </div>

        <button type="submit" class="btn btn-danger" style="width: 100%; padding: 12px; margin-top: 20px;">
            <span>Deduct & File Damage Log</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
            </svg>
        </button>
    </form>
</div>

<!-- Interactive UI checks -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('quantity');
    const helperText = document.getElementById('stock-helper-text');

    productSelect.addEventListener('change', () => {
        const selectedOpt = productSelect.options[productSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const stock = parseInt(selectedOpt.getAttribute('data-stock')) || 0;
            helperText.textContent = `* Available stock: \${stock} units.`;
            qtyInput.max = stock;
            qtyInput.placeholder = `Max: \${stock}`;
        }
    });

    qtyInput.addEventListener('input', () => {
        const selectedOpt = productSelect.options[productSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const stock = parseInt(selectedOpt.getAttribute('data-stock')) || 0;
            const entered = parseInt(qtyInput.value) || 0;
            if (entered > stock) {
                alert(`Warning: Damaged quantity exceeds available stock levels of \${stock} units.`);
                qtyInput.value = stock;
            }
        }
    });
});
</script>
