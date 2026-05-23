<div class="card-panel" style="max-width: 500px; margin: 0 auto;">
    <div class="card-title">
        <span>🔄 Restock Inventory Item</span>
        <a href="<?= $this->getUrl('products') ?>" class="btn btn-secondary btn-sm">Cancel</a>
    </div>

    <div style="background: rgba(255,255,255,0.01); border: 1px solid var(--border); padding:20px; border-radius:10px; margin-bottom:24px;">
        <h3 style="font-size:1.1rem; color:#ffffff;"><?= htmlspecialchars($product['name']) ?></h3>
        <p style="color:var(--text-secondary); font-size:0.85rem; margin-top:4px;">SKU Code: <code><?= htmlspecialchars($product['sku']) ?></code></p>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px; border-top:1px solid var(--border); padding-top:16px;">
            <span style="font-size:0.9rem; color:var(--text-secondary);">Current Stock:</span>
            <span style="font-weight:700; font-size:1.2rem; color:var(--success);"><?= $product['stock_quantity'] ?> units</span>
        </div>
    </div>

    <form action="<?= $this->getUrl("products/restock/{$product['id']}") ?>" method="POST">
        <div class="form-group">
            <label for="added_qty">Units to Add *</label>
            <input type="number" id="added_qty" name="added_qty" min="1" class="form-control" placeholder="e.g. 50" required autofocus>
            <small style="color:var(--text-muted); display:block; margin-top:6px;">This action will increment the inventory count in real-time.</small>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; margin-top:10px;">
            <span>Increase Stock Level</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                <polyline points="17 6 23 6 23 12"/>
            </svg>
        </button>
    </form>
</div>
