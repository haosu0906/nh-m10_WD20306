<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Đánh giá HDV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
    :root {
        --accent: #667eea;
        --accent-dark: #5568d3
    }

    .rating-container {
        max-width: 800px;
        margin: 2rem auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .rating-header {
        background: linear-gradient(135deg, var(--accent), #764ba2);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .guide-info {
        background: #f8f9fa;
        padding: 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }

    .rating-stars {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        justify-content: center;
    }

    .star-input {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: all 0.2s;
    }

    .star-input:hover,
    .star-input.active {
        color: #ffc107;
        transform: scale(1.1);
    }

    .rating-section {
        background: white;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .rating-label {
        font-weight: 600;
        margin-bottom: 1rem;
        color: #495057;
        text-align: center;
    }

    .rating-value {
        font-size: 0.875rem;
        color: #6c757d;
        text-align: center;
        margin-top: 0.5rem;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--accent), #764ba2);
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 25px;
        transition: transform 0.2s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    body {
        background: linear-gradient(135deg, #667eea, #764ba2);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .tour-badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid #c3e6cb;
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid #f5c6cb;
    }
    </style>
</head>

<body>
    <div class="rating-container">
        <!-- Header -->
        <div class="rating-header">
            <h3 class="mb-3"><i class="fas fa-star"></i> Đánh giá Hướng dẫn viên</h3>
            <p class="mb-0">Hãy chia sẻ trải nghiệm của bạn để giúp chúng tôi cải thiện dịch vụ</p>
        </div>

        <!-- Guide Info -->
        <div class="guide-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-2">
                        <i class="fas fa-user-tie"></i> 
                        <?= htmlspecialchars($guide['full_name']) ?>
                    </h5>
                    <div class="tour-badge">
                        <i class="fas fa-route"></i> 
                        <strong>Tour:</strong> <?= htmlspecialchars($booking['title']) ?> - 
                        <?= date('d/m/Y', strtotime($booking['date_booked'])) ?>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="text-muted small">Mã booking</div>
                    <strong>#<?= htmlspecialchars($booking['id']) ?></strong>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="error-message mx-3 mt-3">
            <?= $_SESSION['flash_error'] ?>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="success-message mx-3 mt-3">
            <?= $_SESSION['flash_success'] ?>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>?r=guide_ratings_store" class="p-3">
            <input type="hidden" name="guide_id" value="<?= $guide['id'] ?>">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <input type="hidden" name="schedule_id" value="0">
            <input type="hidden" name="rater_type" value="customer">
            <input type="hidden" name="rater_id" value="<?= $booking['customer_id'] ?? 1 ?>">

            <!-- Rating Sections -->
            <div class="row">
                <div class="col-md-6">
                    <div class="rating-item">
                        <div class="rating-label">Đánh giá chung</div>
                        <div class="rating-stars" data-rating="overall">
                            <i class="fas fa-star star-input" data-value="1"></i>
                            <i class="fas fa-star star-input" data-value="2"></i>
                            <i class="fas fa-star star-input" data-value="3"></i>
                            <i class="fas fa-star star-input" data-value="4"></i>
                            <i class="fas fa-star star-input" data-value="5"></i>
                        </div>
                        <input type="hidden" name="rating" value="5" required>
                        <div class="rating-value">Điểm: <span class="rating-display">5.0</span>/5.0</div>
                    </div>
                </div>
            </div>

            <!-- Comments -->
            <div class="rating-section">
                <h6 class="mb-3"><i class="fas fa-comment"></i> Chia sẻ trải nghiệm của bạn</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nhận xét chung <span class="text-danger">*</span></label>
                        <textarea name="comment" rows="4" class="form-control" placeholder="Hãy chia sẻ cảm nhận của bạn về hướng dẫn viên..." required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Điều bạn thích nhất</label>
                        <textarea name="pros" rows="3" class="form-control" placeholder="Những điểm bạn thấy HDV làm tốt..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Góp ý cải thiện</label>
                        <textarea name="cons" rows="3" class="form-control" placeholder="Những điểm HDV có thể cải thiện..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="text-center pb-3">
                <button type="submit" class="btn btn-primary btn-submit">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
                <a href="<?= BASE_URL ?>?r=booking_detail&id=<?= $booking['id'] ?>" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>

    <script>
    // Rating stars interaction
    document.addEventListener('DOMContentLoaded', function() {
        const ratingContainers = document.querySelectorAll('.rating-stars');
        
        ratingContainers.forEach(container => {
            const stars = container.querySelectorAll('.star-input');
            const input = container.nextElementSibling;
            const display = container.nextElementSibling.nextElementSibling.querySelector('.rating-display');
            
            function setRating(value) {
                input.value = value;
                display.textContent = value + '.0';
                
                stars.forEach((star, index) => {
                    if (index < value) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }
            
            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    setRating(index + 1);
                });
                
                star.addEventListener('mouseenter', function() {
                    stars.forEach((s, i) => {
                        if (i <= index) {
                            s.style.color = '#ffc107';
                            s.style.transform = 'scale(1.1)';
                        } else {
                            s.style.color = '#ddd';
                            s.style.transform = 'scale(1)';
                        }
                    });
                });
            });
            
            container.addEventListener('mouseleave', function() {
                const currentValue = parseInt(input.value);
                stars.forEach((s, i) => {
                    if (i < currentValue) {
                        s.style.color = '#ffc107';
                        s.style.transform = 'scale(1)';
                    } else {
                        s.style.color = '#ddd';
                        s.style.transform = 'scale(1)';
                    }
                });
            });
        });
    });
    </script>
</body>

</html>
