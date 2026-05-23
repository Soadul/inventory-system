<div class="card-panel" style="max-width: 900px; margin: 0 auto;">
    <div class="card-title">
        <span>🛒 Input New Daily Sales Transaction</span>
        <a href="<?= $this->getUrl('sales') ?>" class="btn btn-secondary btn-sm">Cancel</a>
    </div>

    <form action="<?= $this->getUrl('sales/create') ?>" method="POST" id="sales-form">
        
        <div style="margin-bottom: 20px;">
            <p style="color:var(--text-secondary); font-size: 0.9rem; margin-bottom: 12px;">Add items to this daily sale register. Stock is automatically depleted on successful transmit.</p>
        </div>

        <!-- Sales Dynamic Rows Grid -->
        <div class="sales-product-grid" id="product-rows-container">
            <!-- Header Row -->
            <div class="sales-row" style="background:rgba(255,255,255,0.02); font-weight:600; border-color:var(--border); display:grid; grid-template-columns: 2.2fr 1fr 1.2fr 0.2fr;">
                <div>Product Name *</div>
                <div>Quantity *</div>
                <div>Subtotal Price</div>
                <div></div>
            </div>

            <!-- Initial Default Row -->
            <div class="sales-row product-entry-row" style="display:grid; grid-template-columns: 2.2fr 1fr 1.2fr 0.2fr;">
                <div>
                    <select name="product_ids[]" class="form-control prod-select" required>
                        <option value="" disabled selected>-- Select Product --</option>
                        <?php foreach ($products as $prod): ?>
                            <option value="<?= $prod['id'] ?>" data-price="<?= $prod['price'] ?>" data-stock="<?= $prod['stock_quantity'] ?>">
                                <?= htmlspecialchars($prod['name']) ?> (Price: <?= $prod['price'] ?> TK | Stock: <?= $prod['stock_quantity'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <input type="number" name="quantities[]" class="form-control prod-qty" min="1" placeholder="Qty" required>
                </div>
                <div class="row-subtotal-display" style="font-weight:700; font-size:1rem; color:var(--text-secondary);">
                    0.00 TK
                </div>
                <div style="text-align: center;">
                    <button type="button" class="remove-row-btn" onclick="removeSalesRow(this)">&times;</button>
                </div>
            </div>
        </div>

        <!-- Control Action to Add Item Row -->
        <button type="button" class="btn btn-secondary btn-sm" id="add-row-btn" style="margin-bottom: 30px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Add Another Product</span>
        </button>

        <div style="border-top: 1px solid var(--border); padding-top: 30px; display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: flex-start;">
            <!-- Left Side Payment configurations -->
            <div>
                <div class="sales-row" style="grid-template-columns: 1fr 1fr; background:none; border:none; padding:0; gap:20px;">
                    <div class="form-group">
                        <label for="paid_amount">Cash Paid Collected (TK) *</label>
                        <input type="number" id="paid_amount" name="paid_amount" step="0.01" min="0" class="form-control" placeholder="0.00" required>
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Payment Mode *</label>
                        <select id="payment_method" name="payment_method" class="form-control">
                            <option value="Cash">Cash</option>
                            <option value="bKash">bKash Mobile Banking</option>
                            <option value="Nagad">Nagad Mobile Banking</option>
                            <option value="Bank Transfer">Bank Wire Transfer</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Right Side Invoice Totals Review Card -->
            <div style="background: rgba(255,255,255,0.015); border: 1px solid var(--border); border-radius:12px; padding:24px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:0.95rem; color:var(--text-secondary);">
                    <span>Subtotal Value:</span>
                    <span id="grand-subtotal-val" style="font-weight:700; color:#ffffff;">0.00 TK</span>
                </div>
                
                <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:0.95rem; color:var(--text-secondary); border-top:1px dashed var(--border); padding-top:12px;">
                    <span>Collected Cash:</span>
                    <span id="grand-collected-val" style="font-weight:700; color:var(--success);">0.00 TK</span>
                </div>

                <div style="display:flex; justify-content:space-between; font-size:1.1rem; color:var(--text-secondary); border-top:1px solid var(--border); padding-top:16px;">
                    <span style="font-weight:600; color:#ffffff;">Remaining Due Credit:</span>
                    <span id="grand-due-val" style="font-weight:800; color:var(--warning);">0.00 TK</span>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; margin-top: 30px;">
            <span>Transmit Daily Sales Invoice</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="22" y1="2" x2="11" y2="13"/>
                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
        </button>
    </form>
</div>

<!-- Dynamic Javascript calculation bindings -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('product-rows-container');
    const addRowBtn = document.getElementById('add-row-btn');
    const paidInput = document.getElementById('paid_amount');
    
    const subtotalText = document.getElementById('grand-subtotal-val');
    const collectedText = document.getElementById('grand-collected-val');
    const dueText = document.getElementById('grand-due-val');

    // Dynamic row addition template
    addRowBtn.addEventListener('click', () => {
        const firstRow = document.querySelector('.product-entry-row');
        const cloned = firstRow.cloneNode(true);
        
        // Reset selections and fields in clone
        const select = cloned.querySelector('select');
        select.selectedIndex = 0;
        cloned.querySelector('input').value = '';
        cloned.querySelector('.row-subtotal-display').textContent = '0.00 TK';
        
        // Bind dynamic listener
        bindRowEvents(cloned);
        
        container.appendChild(cloned);
    });

    function bindRowEvents(row) {
        const select = row.querySelector('.prod-select');
        const qty = row.querySelector('.prod-qty');

        const recomputeRow = () => {
            const selectedOpt = select.options[select.selectedIndex];
            const qtyVal = parseInt(qty.value) || 0;
            let subtotal = 0.00;

            if (selectedOpt && selectedOpt.value) {
                const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0.00;
                const maxStock = parseInt(selectedOpt.getAttribute('data-stock')) || 0;
                
                // Warn client-side on stock limit
                if (qtyVal > maxStock) {
                    alert(`Stock alert! Only \${maxStock} units of this item are currently in stock.`);
                    qty.value = maxStock;
                    subtotal = price * maxStock;
                } else {
                    subtotal = price * qtyVal;
                }
            }

            row.querySelector('.row-subtotal-display').textContent = subtotal.toFixed(2) + ' TK';
            calculateGrandTotals();
        };

        select.addEventListener('change', recomputeRow);
        qty.addEventListener('input', recomputeRow);
    }

    function calculateGrandTotals() {
        let subtotalAccumulator = 0.00;
        
        const rows = document.querySelectorAll('.product-entry-row');
        rows.forEach(row => {
            const select = row.querySelector('.prod-select');
            const qty = row.querySelector('.prod-qty');
            const selectedOpt = select.options[select.selectedIndex];
            
            if (selectedOpt && selectedOpt.value) {
                const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0.00;
                const qtyVal = parseInt(qty.value) || 0;
                subtotalAccumulator += price * qtyVal;
            }
        });

        const paidVal = parseFloat(paidInput.value) || 0.00;
        
        // Warn if payment exceeds subtotal
        if (paidVal > subtotalAccumulator && subtotalAccumulator > 0) {
            alert("Warning: Collected cash exceeds invoice total!");
            paidInput.value = subtotalAccumulator.toFixed(2);
        }

        const rePaid = parseFloat(paidInput.value) || 0.00;
        const dueVal = Math.max(0.00, subtotalAccumulator - rePaid);

        subtotalText.textContent = subtotalAccumulator.toFixed(2) + ' TK';
        collectedText.textContent = rePaid.toFixed(2) + ' TK';
        dueText.textContent = dueVal.toFixed(2) + ' TK';
    }

    // Bind events to default initial row
    document.querySelectorAll('.product-entry-row').forEach(row => {
        bindRowEvents(row);
    });

    paidInput.addEventListener('input', calculateGrandTotals);

    // Global trigger for remove
    window.removeSalesRow = function(btn) {
        const rows = document.querySelectorAll('.product-entry-row');
        if (rows.length <= 1) {
            alert("Your sale transaction must contain at least one product row.");
            return;
        }
        btn.closest('.sales-row').remove();
        calculateGrandTotals();
    };
});
</script>
