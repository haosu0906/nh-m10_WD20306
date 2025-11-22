<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Quản lý HDV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3
    }

    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        width: 200px;
        padding: 20px;
        background: linear-gradient(180deg, var(--accent), #764ba2);
        color: #fff;
        overflow: auto
    }

    .sidebar h3 {
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        font-size: 16px
    }

    .nav-link {
        color: rgba(255, 255, 255, .95);
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .6rem;
        border-radius: .5rem;
        text-decoration: none
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, .1)
    }

    .main {
        margin-left: 200px;
        padding: 22px
    }

    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3><i class="fas fa-map-marked-alt"></i> Quản trị Tripmate</h3>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?= BASE_URL ?>?r=home"><i class="fas fa-tachometer-alt"></i> Tổng quan</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tour_categories"><i class="fas fa-map"></i> Danh mục tour</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=tours"><i class="fas fa-route"></i> Tours</a>
            <a class="nav-link active" href="<?= BASE_URL ?>?r=guides"><i class="fas fa-user-tie"></i> HDV</a>
            <a class="nav-link" href="<?= BASE_URL ?>?r=staff"><i class="fas fa-users"></i> Nhân Sự</a>
        </nav>
    </div>

    <main class="main">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Danh sách hướng dẫn viên</h3>
                <p class="text-muted mb-0">Quản lý hồ sơ, phân loại và lọc theo tiêu chí</p>
            </div>
            <a class="btn btn-success" href="<?= BASE_URL ?>?r=guides_create"><i class="fas fa-plus"></i> Thêm HDV</a>
        </div>

        <form class="card mb-3" method="get">
            <input type="hidden" name="r" value="guides">
            <div class="card-body row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" class="form-control" name="q" placeholder="Tên, email hoặc SĐT"
                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Loại HDV</label>
                    <select class="form-select" name="type">
                        <option value="">Tất cả</option>
                        <?php foreach ($types as $key => $label): ?>
                        <option value="<?= $key ?>" <?= (($_GET['type'] ?? '') === $key) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" type="submit"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Họ tên</th>
                            <th>Liên hệ</th>
                            <th>Giấy tờ</th>
                            <th>Loại</th>
                            <th>Ghi chú</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($guides)): foreach($guides as $guide): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if(!empty($guide['avatar'])): ?>
                                    <img src="<?= BASE_ASSETS_UPLOADS . $guide['avatar'] ?>" class="avatar"
                                        alt="<?= htmlspecialchars($guide['full_name']) ?>">
                                    <?php else: ?>
                                    <div
                                        class="avatar bg-light d-flex justify-content-center align-items-center text-muted">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($guide['full_name']) ?></div>
                                        <?php
                        $createdDisplay = !empty($guide['created_at']) ? date('d/m/Y', strtotime($guide['created_at'])) : '---';
                      ?>
                                        <small class="text-muted"><?= $createdDisplay ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>Email: <?= htmlspecialchars($guide['email']) ?></div>
                                <div>ĐT: <?= htmlspecialchars($guide['phone']) ?></div>
                            </td>
                            <td>
                                <div>CMND: <?= htmlspecialchars($guide['identity_no']) ?></div>
                                <div>Chứng chỉ: <?= htmlspecialchars($guide['certificate_no']) ?></div>
                            </td>
                            <td><span
                                    class="badge bg-primary"><?= htmlspecialchars($types[$guide['guide_type']] ?? $guide['guide_type']) ?></span>
                            </td>
                            <td><?= htmlspecialchars(mb_substr($guide['notes'] ?? '', 0, 40)) ?></td>
                            <td>
                                <a class="btn btn-sm btn-primary"
                                    href="<?= BASE_URL ?>?r=guides_edit&id=<?= $guide['id'] ?>">Sửa</a>
                                <a class="btn btn-sm btn-danger"
                                    href="<?= BASE_URL ?>?r=guides_delete&id=<?= $guide['id'] ?>"
                                    onclick="return confirm('Xóa hướng dẫn viên này?')">Xóa</a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có dữ liệu</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>