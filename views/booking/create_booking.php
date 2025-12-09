<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Tạo Booking mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3;
    }

    body {
        background: #f8f9fa;
    }

    

    .sidebar h3 {
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        font-size: 16px;
    }

    
    </style>
</head>

<body>
    <?php
        $current_page = 'booking';
        require_once __DIR__ . '/../../assets/templates/sidebar.php';
    ?>
    <?php require_once __DIR__ . '/../../assets/templates/topbar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Tạo Booking mới</h3>
                <p class="text-muted mb-0">Chọn tour, ngày khởi hành và nhập danh sách khách</p>
            </div>
            <a href="<?= BASE_URL ?>?r=booking" class="btn btn-outline-secondary">Quay lại danh sách</a>
        </div>

        <?php
        $flash = flash_get();
        if ($flash):
        ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>?r=booking_store">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <div class="card mb-4">
                <div class="card-header fw-semibold">Thông tin booking</div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tour / Lịch khởi hành</label>
                        <select class="form-select" name="schedule_id" id="schedule-select" required>
                            <option value="">-- Chọn tour và ngày khởi hành --</option>
                            <?php foreach ($schedules as $s): ?>
                            <?php
                                $tid = (int)($s['tour_id'] ?? 0);
                                $supLabel = $suppliersByTour[$tid] ?? '';
                            ?>
                            <?php $tp = $tourPricesById[$tid] ?? ['adult'=>0,'child'=>0,'infant'=>0]; ?>
                            <option value="<?= $s['id'] ?>"
                                    data-tour-id="<?= $s['tour_id'] ?>"
                                    data-supplier="<?= htmlspecialchars($supLabel) ?>"
                                    data-price-adult="<?= htmlspecialchars($s['price_adult'] ?? 0) ?>"
                                    data-price-child="<?= htmlspecialchars($s['price_child'] ?? 0) ?>"
                                    data-price-infant="<?= htmlspecialchars($s['price_infant'] ?? 0) ?>"
                                    data-tour-price-adult="<?= htmlspecialchars($tp['adult'] ?? 0) ?>"
                                    data-tour-price-child="<?= htmlspecialchars($tp['child'] ?? 0) ?>"
                                    data-tour-price-infant="<?= htmlspecialchars($tp['infant'] ?? 0) ?>">
                                <?= htmlspecialchars($s['tour_title']) ?> -
                                <?= htmlspecialchars($s['start_date']) ?>
                                <?php if ($supLabel !== ''): ?>
                                    - NCC: <?= htmlspecialchars($supLabel) ?>
                                <?php endif; ?>
                                (Tối đa: <?= (int)$s['max_capacity'] ?> khách)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="tour_id" id="tour-id-hidden" />
                        <div class="form-text" id="supplier-info">
                            Vui lòng chọn tour để xem nhà cung cấp / dịch vụ đi kèm.
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tổng số khách</label>
                        <input type="number" min="1" class="form-control" name="total_guests" id="total-guests"
                            value="1" required />
                        <div class="form-text">Ghế dự kiến dùng = NL + Trẻ em</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Số NL / Trẻ em / Em bé</label>
                        <div class="d-flex gap-2">
                            <input type="number" min="0" class="form-control" id="count-adult" value="1" title="Người lớn" />
                            <input type="number" min="0" class="form-control" id="count-child" value="0" title="Trẻ em" />
                            <input type="number" min="0" class="form-control" id="count-infant" value="0" title="Em bé" />
                        </div>
                        <div class="form-text">Tự tạo dòng khách và gán loại</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Khách hàng</label>
                        <select class="form-select" name="customer_user_id">
                            <option value="">-- Chọn khách hàng (users.role = traveler) --</option>
                            <?php if (!empty($customers)): foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>">
                                <?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['email']) ?>)
                            </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">Giá niêm yết</div>
                <div class="card-body row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Giá người lớn</label>
                        <input type="number" class="form-control" id="price-adult" value="0" readonly />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Giá trẻ em</label>
                        <input type="number" class="form-control" id="price-child" value="0" readonly />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Giá em bé</label>
                        <input type="number" class="form-control" id="price-infant" value="0" readonly />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tổng tiền ước tính</label>
                        <input type="text" class="form-control" id="total-price-preview" value="0" readonly />
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Thông tin khách hàng</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-guest-btn">
                        <i class="fas fa-user-plus"></i> Thêm khách
                    </button>
                </div>
                <div class="card-body" id="guests-container">
                    <div class="row g-2 guest-row mb-3">
                        <div class="col-md-3">
                            <input type="text" name="guest_full_name[]" class="form-control" placeholder="Họ tên"
                                required />
                        </div>
                        <div class="col-md-2">
                            <select name="guest_gender[]" class="form-select">
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="guest_dob[]" class="form-control" />
                        </div>
                        <div class="col-md-2">
                            <select name="guest_type[]" class="form-select guest-type">
                                <option value="adult">Người lớn</option>
                                <option value="child">Trẻ em</option>
                                <option value="infant">Em bé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="guest_id_document_no[]" class="form-control"
                                placeholder="CMND/CCCD" />
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <input type="text" name="guest_notes[]" class="form-control" placeholder="Ghi chú" />
                            <button type="button" class="btn btn-outline-danger btn-remove-guest">&times;</button>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <div class="me-auto text-start"><strong>Tạm tính:</strong> <span id="total-price-text">0</span> đ</div>
                    <button type="submit" class="btn btn-primary">Tạo booking</button>
                </div>
            </div>
        </form>
    </div>

    <script>
    // Gán tour_id theo schedule được chọn
    const scheduleSelect = document.getElementById('schedule-select');
    const tourHidden = document.getElementById('tour-id-hidden');
    const supplierInfo = document.getElementById('supplier-info');
    const priceAdultEl = document.getElementById('price-adult');
    const priceChildEl = document.getElementById('price-child');
    const priceInfantEl = document.getElementById('price-infant');
    const totalPricePreviewEl = document.getElementById('total-price-preview');
    const totalPriceTextEl = document.getElementById('total-price-text');
    if (scheduleSelect && tourHidden) {
        scheduleSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            tourHidden.value = opt.getAttribute('data-tour-id') || '';

            if (supplierInfo) {
                const sup = opt.getAttribute('data-supplier') || '';
                // Hiện NCC chính
                supplierInfo.textContent = sup ? ('NCC chính: ' + sup) : 'Tour này chưa gán nhà cung cấp chính.';
                // Tải danh sách dịch vụ NCC theo tour
                const tid = opt.getAttribute('data-tour-id');
                if (tid) {
                    fetch('<?= BASE_URL ?>?r=tour_suppliers_json&tour_id=' + encodeURIComponent(tid))
                        .then(function(r){ return r.json(); })
                        .then(function(rows){
                            if (Array.isArray(rows) && rows.length) {
                                const map = {hotel:'Khách sạn',restaurant:'Nhà hàng',transport:'Vận chuyển',ticket:'Vé tham quan',insurance:'Bảo hiểm',guide:'HDV',meal:'Ăn uống',entertain:'Giải trí',other:'Dịch vụ khác'};
                                const list = rows.map(function(r){
                                    const raw = (r.service_type||'').toLowerCase();
                                    const svc = map[raw] || (r.service_type || 'Dịch vụ');
                                    const name = r.name || 'NCC';
                                    const desc = r.service_description ? (': ' + r.service_description.replace(/^service:/i,'')) : '';
                                    return '- ' + name + ' (' + svc + ')' + desc;
                                }).join('\n');
                                supplierInfo.textContent = (sup ? ('NCC chính: ' + sup + '\n') : '') + 'Dịch vụ NCC cho tour:\n' + list;
                            }
                        }).catch(function(){ /* ignore */ });
                }
            }

            // Tải giá niêm yết theo schedule bằng API đơn giản: lấy từ option data-* nếu có hoặc yêu cầu endpoint khác
            // Ở đây giả sử server đã render các thuộc tính data-price-*
            let pa = parseFloat(opt.getAttribute('data-price-adult') || '0');
            let pc = parseFloat(opt.getAttribute('data-price-child') || '0');
            let pi = parseFloat(opt.getAttribute('data-price-infant') || '0');
            const tpa = parseFloat(opt.getAttribute('data-tour-price-adult') || '0');
            const tpc = parseFloat(opt.getAttribute('data-tour-price-child') || '0');
            const tpi = parseFloat(opt.getAttribute('data-tour-price-infant') || '0');
            if (!pa || pa <= 0) pa = tpa || 0;
            if (!pc || pc <= 0) pc = tpc || 0;
            if (!pi || pi <= 0) pi = tpi || 0;
            if (priceAdultEl) priceAdultEl.value = isNaN(pa) ? 0 : pa;
            if (priceChildEl) priceChildEl.value = isNaN(pc) ? 0 : pc;
            if (priceInfantEl) priceInfantEl.value = isNaN(pi) ? 0 : pi;
            recalcTotal();
        });
    }

    // Form khách dynamic
    const guestsContainer = document.getElementById('guests-container');
    const addGuestBtn = document.getElementById('add-guest-btn');
    const countAdultEl = document.getElementById('count-adult');
    const countChildEl = document.getElementById('count-child');
    const countInfantEl = document.getElementById('count-infant');

    function attachRemoveHandlers() {
        document.querySelectorAll('.btn-remove-guest').forEach(function(btn) {
            btn.onclick = function() {
                const row = this.closest('.guest-row');
                if (document.querySelectorAll('.guest-row').length > 1) {
                    row.remove();
                }
            };
        });
    }

    if (addGuestBtn && guestsContainer) {
        addGuestBtn.addEventListener('click', function() {
            const firstRow = document.querySelector('.guest-row');
            const clone = firstRow.cloneNode(true);
            // Sao chép dữ liệu từ hàng đầu
            const mapNames = ['guest_full_name[]','guest_gender[]','guest_dob[]','guest_id_document_no[]','guest_notes[]','guest_type[]'];
            mapNames.forEach(function(name){
                const src = firstRow.querySelector('[name="'+name+'"]');
                const dst = clone.querySelector('[name="'+name+'"]');
                if (src && dst) {
                    if (dst.tagName === 'SELECT') { dst.value = src.value; }
                    else { dst.value = src.value; }
                }
            });
            guestsContainer.appendChild(clone);
            attachRemoveHandlers();
            attachTypeChange();
            recalcTotal();
        });
        attachRemoveHandlers();
    }

    // Validate cơ bản trước khi submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!scheduleSelect.value) {
                alert('Vui lòng chọn tour / lịch khởi hành');
                e.preventDefault();
                return;
            }

            const totalGuestsInput = document.getElementById('total-guests');
            const total = parseInt(totalGuestsInput.value || '0', 10);
            if (isNaN(total) || total <= 0) {
                alert('Số lượng khách phải lớn hơn 0');
                e.preventDefault();
                return;
            }

            const names = Array.from(document.querySelectorAll('input[name="guest_full_name[]"]'))
                .map(function(i) {
                    return i.value.trim();
                })
                .filter(function(v) {
                    return v !== '';
                });

            if (names.length !== total) {
                if (!confirm('Số lượng khách nhập (' + names.length + ') khác với tổng số khách (' + total +
                        '). Bạn vẫn muốn tiếp tục?')) {
                    e.preventDefault();
                    return;
                }
            }
        });
    }

    function attachTypeChange(){
        document.querySelectorAll('.guest-type').forEach(function(sel){
            sel.onchange = function(){ recalcTotal(); };
        });
    }
    attachTypeChange();

    function recalcTotal(){
        const pa = parseFloat(priceAdultEl?.value || '0') || 0;
        const pc = parseFloat(priceChildEl?.value || '0') || 0;
        const pi = parseFloat(priceInfantEl?.value || '0') || 0;
        let ca=0, cc=0, ci=0;
        document.querySelectorAll('select[name="guest_type[]"]').forEach(function(sel){
            const v = sel.value;
            if (v==='adult') ca++; else if (v==='child') cc++; else if (v==='infant') ci++;
        });
        const total = (ca*pa) + (cc*pc) + (ci*pi);
        if (totalPricePreviewEl) totalPricePreviewEl.value = total.toFixed(0);
        if (totalPriceTextEl) totalPriceTextEl.textContent = (total||0).toLocaleString('vi-VN');
        const totalGuestsInput = document.getElementById('total-guests');
        if (totalGuestsInput) totalGuestsInput.value = (ca+cc+ci);
    }

    function ensureGuestRows(target){
        const curRows = document.querySelectorAll('.guest-row');
        const diff = target - curRows.length;
        if (diff > 0){
            for (let i=0;i<diff;i++){
                const firstRow = document.querySelector('.guest-row');
                const clone = firstRow.cloneNode(true);
                // Sao chép dữ liệu từ hàng đầu để tiết kiệm thời gian demo
                const mapNames = ['guest_full_name[]','guest_gender[]','guest_dob[]','guest_id_document_no[]','guest_notes[]','guest_type[]'];
                mapNames.forEach(function(name){
                    const src = firstRow.querySelector('[name="'+name+'"]');
                    const dst = clone.querySelector('[name="'+name+'"]');
                    if (src && dst) {
                        if (dst.tagName === 'SELECT') { dst.value = src.value; }
                        else { dst.value = src.value; }
                    }
                });
                guestsContainer.appendChild(clone);
            }
            attachRemoveHandlers(); attachTypeChange();
        } else if (diff < 0){
            for (let i=0;i<Math.abs(diff);i++){
                const rows = document.querySelectorAll('.guest-row');
                if (rows.length>1) rows[rows.length-1].remove();
            }
        }
    }

    function applyCounts(){
        const a = Math.max(0, parseInt(countAdultEl?.value||'0',10)||0);
        const c = Math.max(0, parseInt(countChildEl?.value||'0',10)||0);
        const i = Math.max(0, parseInt(countInfantEl?.value||'0',10)||0);
        const target = a + c + i;
        ensureGuestRows(target);
        const sels = Array.from(document.querySelectorAll('select[name="guest_type[]"]'));
        let idx=0;
        for (let k=0;k<a && idx<sels.length;k++,idx++) sels[idx].value='adult';
        for (let k=0;k<c && idx<sels.length;k++,idx++) sels[idx].value='child';
        for (let k=0;k<i && idx<sels.length;k++,idx++) sels[idx].value='infant';
        recalcTotal();
    }

    [countAdultEl, countChildEl, countInfantEl].forEach(function(el){ if (el) el.addEventListener('input', applyCounts); });
    applyCounts();
    </script>
</body>

</html>
