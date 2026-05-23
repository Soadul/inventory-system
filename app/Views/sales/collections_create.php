<div class="card-panel" style="max-width: 600px; margin: 0 auto;">
    <div class="card-title">
        <span>💰 Record Cash Collection from Credit Account</span>
        <a href="<?= $this->getUrl('sales/collections') ?>" class="btn btn-secondary btn-sm">Cancel</a>
    </div>

    <form action="<?= $this->getUrl('sales/collections/create') ?>" method="POST">
        
        <div class="form-group">
            <label for="sale_id">Select Outstanding Invoice Due *</label>
            <select id="sale_id" name="sale_id" class="form-control" required autofocus>
                <option value="" disabled selected>-- Select Invoice --</option>
                <?php foreach ($creditSales as $cSale): ?>
                    <option value="<?= $cSale['id'] ?>" data-due="<?= $cSale['due_amount'] ?>">
                        Invoice #<?= $cSale['id'] ?> (Salesman: <?= htmlspecialchars($cSale['salesman_name']) ?> | Due: <?= $cSale['due_amount'] ?> TK)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="customer_name">Customer / Shop Name *</label>
            <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="e.g. Al-Amin Grocery" required>
        </div>

        <div class="form-group">
            <label for="amount">Collected Amount (TK) *</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" class="form-control" placeholder="0.00" required>
            <small id="due-helper-text" style="color:var(--text-muted); display:block; margin-top:4px;"></small>
        </div>

        <div class="form-group">
            <label for="notes">Collection Notes / Reference</label>
            <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="e.g. Received cash payment from owner..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
            <span>Record Cash Collection</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
            </svg>
        </button>
    </form>
</div>

<!-- Interactive UI validations -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const saleSelect = document.getElementById('sale_id');
    const amountInput = document.getElementById('amount');
    const helperText = document.getElementById('due-helper-text');

    saleSelect.addEventListener('change', () => {
        const selectedOpt = saleSelect.options[saleSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const dueAmount = parseFloat(selectedOpt.getAttribute('data-due')) || 0.00;
            helperText.textContent = `* Outstanding credit due on this invoice is ${dueAmount.toFixed(2)} TK. Amount cannot exceed this.`;
            amountInput.max = dueAmount;
            amountInput.placeholder = `Max: \${dueAmount.toFixed(2)}`;
        }
    });

    amountInput.addEventListener('input', () => {
        const selectedOpt = saleSelect.options[saleSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const dueAmount = parseFloat(selectedOpt.getAttribute('data-due')) || 0.00;
            const entered = parseFloat(amountInput.value) || 0.00;
            if (entered > dueAmount) {
                alert(`Warning: Collected cash amount exceeds the pending credit due level of \${dueAmount.toFixed(2)} TK.`);
                amountInput.value = dueAmount.toFixed(2);
            }
        }
    });
});
</script>
