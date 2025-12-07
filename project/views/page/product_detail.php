<?php
// Các biến: $book, $variants, $images, $relatedProducts
?>

<!-- Hiển thị thông báo -->
<?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
    <?php 
      echo htmlspecialchars($_SESSION['success']); 
      unset($_SESSION['success']);
    ?>
  </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
  <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
    <?php 
      echo htmlspecialchars($_SESSION['error']); 
      unset($_SESSION['error']);
    ?>
  </div>
<?php endif; ?>

<style>
/* Màu chính của web - GIỐNG BANNER */
:root {
    --primary-color: #0fbfbf;
    --primary-hover: #0aa5a5;
    --primary-light: #e0f9f9;
    --primary-dark: #088a8a;
}

/* Nút quay lại ở trên */
.back-button-top {
    margin-bottom: 20px;
}

.btn-back-top {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-back-top:hover {
    background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
}

.product-container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(15, 191, 191, 0.15);
    margin-bottom: 30px;
    border: 1px solid rgba(15, 191, 191, 0.2);
}

.main-image {
    border: 2px solid var(--primary-light);
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    background: linear-gradient(135deg, #f0fffe 0%, var(--primary-light) 100%);
}

.main-image img {
    max-width: 100%;
    height: auto;
}

.thumb-images {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.thumb-item {
    width: 80px;
    height: 100px;
    border: 2px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s;
}

.thumb-item:hover {
    border-color: var(--primary-color);
    transform: scale(1.05);
}

.thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-title {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #1a1a1a;
}

.product-price {
    font-size: 36px;
    color: #dc3545;
    font-weight: bold;
    margin: 20px 0;
}

/* Nút chọn biến thể bìa cứng/mềm */
.variant-selector {
    margin: 25px 0;
    padding: 20px;
    background: linear-gradient(135deg, #f0fffe 0%, var(--primary-light) 100%);
    border-radius: 12px;
    border: 2px solid var(--primary-light);
}

.variant-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.variant-btn {
    flex: 1;
    min-width: 140px;
    padding: 18px 25px;
    border: 3px solid #ddd;
    background: white;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}

.variant-btn:hover {
    border-color: var(--primary-color);
    background: var(--primary-light);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 191, 191, 0.25);
}

.variant-btn.active {
    border-color: var(--primary-color);
    background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(15, 191, 191, 0.4);
}

.variant-btn .format-name {
    font-weight: bold;
    font-size: 17px;
    display: block;
    margin-bottom: 8px;
}

.variant-btn .format-price {
    font-size: 20px;
    color: #dc3545;
    font-weight: bold;
}

.variant-btn.active .format-price {
    color: white;
}

.variant-btn .format-stock {
    font-size: 13px;
    color: #666;
    margin-top: 6px;
}

.variant-btn.active .format-stock {
    color: rgba(255, 255, 255, 0.95);
}

.description-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid var(--primary-color);
    margin-bottom: 25px;
}

/* Chọn số lượng */
.quantity-selector {
    background: var(--primary-light);
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid var(--primary-color);
    margin-bottom: 25px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 12px;
}

.qty-btn {
    width: 45px;
    height: 45px;
    border: 2px solid var(--primary-color);
    background: white;
    color: var(--primary-color);
    font-size: 24px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qty-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: scale(1.1);
}

.qty-btn:active {
    transform: scale(0.95);
}

.qty-btn:disabled {
    background: #e0e0e0;
    border-color: #bdbdbd;
    color: #9e9e9e;
    cursor: not-allowed;
    transform: none;
}

#quantityInput {
    width: 80px;
    height: 45px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    border: 2px solid var(--primary-color);
    border-radius: 8px;
    background: white;
    color: #333;
}

.stock-info {
    color: #666;
    font-size: 14px;
    margin: 0;
    padding-left: 5px;
}

.stock-info span {
    font-weight: bold;
    color: var(--primary-color);
}

/* Nút hành động */
.action-buttons {
    display: flex;
    gap: 15px;
}

.btn-add-cart {
    flex: 1;
    padding: 18px;
    background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    font-weight: bold;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(15, 191, 191, 0.35);
}

.btn-add-cart:hover {
    background: linear-gradient(180deg, var(--primary-hover) 0%, var(--primary-dark) 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15, 191, 191, 0.45);
}

.btn-buy-now {
    flex: 1;
    padding: 18px;
    background: linear-gradient(180deg, #ffc107 0%, #ff9800 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    font-weight: bold;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.35);
}

.btn-buy-now:hover {
    background: linear-gradient(180deg, #ff9800 0%, #f57c00 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.45);
}

/* Phần bình luận và đánh giá */
.review-section {
    margin-top: 40px;
    padding: 30px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(15, 191, 191, 0.15);
    border: 1px solid rgba(15, 191, 191, 0.2);
}

.review-title {
    font-size: 26px;
    font-weight: bold;
    margin-bottom: 30px;
    color: var(--primary-color);
    padding-bottom: 12px;
    border-bottom: 3px solid var(--primary-color);
}

.review-summary {
    text-align: center;
    background: linear-gradient(135deg, #f0fffe 0%, var(--primary-light) 100%);
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.rating-big {
    font-size: 48px;
    font-weight: bold;
    color: var(--primary-color);
}

.rating-stars-big {
    font-size: 28px;
    color: #ffc107;
    margin: 10px 0;
}

.total-reviews {
    color: #666;
    font-size: 16px;
}

/* Form viết bình luận */
.write-review-box {
    background: #f0f9f9;
    padding: 25px;
    border-radius: 12px;
    margin: 25px 0;
    border: 2px solid var(--primary-light);
}

.write-review-box h4 {
    color: #333;
    margin-bottom: 20px;
}

.verified-purchase {
    background: #d4edda;
    color: #155724;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 600;
    border-left: 4px solid #28a745;
}

/* Box thông báo cần mua sản phẩm */
.purchase-required-box {
    background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%);
    padding: 30px;
    border-radius: 12px;
    margin: 25px 0;
    border: 2px solid #ffc107;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
}

.purchase-required-content {
    text-align: center;
}

.purchase-required-content .icon {
    font-size: 64px;
    margin-bottom: 15px;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-15px); }
    60% { transform: translateY(-10px); }
}

.purchase-required-content h4 {
    color: #856404;
    margin-bottom: 10px;
    font-size: 22px;
}

.purchase-required-content p {
    color: #856404;
    margin-bottom: 20px;
    line-height: 1.6;
}

.btn-buy-now {
    display: inline-block;
    background: var(--primary-color);
    color: white;
    padding: 12px 30px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(15, 191, 191, 0.3);
    border: none;
    cursor: pointer;
    font-size: 16px;
}

.btn-buy-now:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(15, 191, 191, 0.4);
    color: white;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

/* Rating sao */
.star-rating-input {
    display: flex;
    gap: 8px;
    font-size: 45px;
    margin: 15px 0;
    justify-content: center;
    padding: 20px;
    background: white;
    border-radius: 12px;
    border: 3px dashed #e0e0e0;
}

.star {
    cursor: pointer;
    color: #ddd;
    transition: all 0.2s ease;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.star:hover {
    transform: scale(1.3) rotate(15deg);
    color: #ffc107;
}

.star.active {
    color: #ffc107;
    transform: scale(1.15);
    animation: starPulse 0.3s ease;
}

@keyframes starPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1.15); }
}

.rating-hint {
    text-align: center;
    color: #999;
    font-size: 14px;
    margin-top: 10px;
    font-style: italic;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
}

textarea.form-control {
    resize: vertical;
}

.btn-submit-review {
    background: var(--primary-color);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
}

.btn-submit-review:hover {
    background: var(--primary-hover);
}

.login-prompt {
    background: #fff3cd;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    margin: 25px 0;
}

.login-prompt a {
    color: var(--primary-color);
    font-weight: bold;
    text-decoration: none;
}

.login-prompt a:hover {
    text-decoration: underline;
}

/* Danh sách bình luận */
.comments-list {
    margin-top: 30px;
}

.comments-list h4 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #333;
}

.comment-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 15px;
    border: 1px solid #e0e0e0;
}

.comment-item:hover {
    border-color: var(--primary-color);
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.comment-header strong {
    color: #333;
    font-size: 16px;
}

.comment-rating {
    font-size: 18px;
    color: #ffc107;
}

.comment-date {
    color: #999;
    font-size: 13px;
    margin-bottom: 10px;
}

.comment-content {
    color: #555;
    line-height: 1.6;
}

.no-comments {
    text-align: center;
    padding: 40px;
    color: #999;
    background: #f8f9fa;
    border-radius: 8px;
}

/* Sản phẩm liên quan */
.related-section {
    margin-top: 50px;
    padding-top: 40px;
    border-top: 3px solid var(--primary-color);
}

.related-title {
    font-size: 26px;
    font-weight: bold;
    margin-bottom: 30px;
    color: var(--primary-color);
    position: relative;
    padding-bottom: 12px;
}

.related-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
    border-radius: 2px;
}

/* Dùng lại CSS product-card từ trang chủ để đồng bộ */
.related-section .product-card {
    border-radius: 8px;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    border: 1px solid #e0e0e0;
}

.related-section .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,.1) !important;
}

.related-section .card-img-container {
    aspect-ratio: 2 / 3;
    background: #f7f7f7;
    display: block;
    position: relative;
}

.related-section .card-img-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.related-section .card-author {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 13px;
    color: #6c757d;
}

.related-section .product-title {
    font-size: 15px;
    font-weight: 600;
    min-height: 42px;
    line-height: 1.4;
    margin-bottom: 8px !important;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.related-section .product-title a {
    text-decoration: none;
    color: inherit;
}

.related-section .product-title a:hover {
    color: var(--primary-color);
}

.related-section .product-price {
    font-size: 17px;
    font-weight: 600;
    color: #dc3545;
}

.related-section .card-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: #ffffff;
    padding: 10px;
    border-top: 1px solid #eee;
    transform: translateY(100%);
    transition: transform 0.3s ease-out;
    z-index: 5;
}

.related-section .product-card:hover .card-footer {
    transform: translateY(0);
}

.related-section .card-footer .btn {
    white-space: nowrap;
    padding: 6px 4px;
    font-size: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 32px;
}

.related-section .btn-wishlist-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
    width: 35px;
    height: 35px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 50%;
    border: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #dc3545;
    font-size: 18px;
    text-decoration: none;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    opacity: 0;
    visibility: hidden;
    transform: scale(0.8);
    transition: all 0.3s ease;
}

.related-section .product-card:hover .btn-wishlist-overlay {
    opacity: 1;
    visibility: visible;
    transform: scale(1);
}

.related-section .btn-wishlist-overlay:hover {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

@media (max-width: 768px) {
    .variant-buttons {
        flex-direction: column;
    }
    
    .variant-btn {
        min-width: 100%;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}

/* ==================== PHẦN Q&A ==================== */
.qa-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(15, 191, 191, 0.15);
    margin-bottom: 30px;
    border: 1px solid rgba(15, 191, 191, 0.2);
}

.qa-title {
    font-size: 28px;
    color: var(--primary-color);
    font-weight: 700;
    margin-bottom: 10px;
    border-bottom: 3px solid var(--primary-color);
    padding-bottom: 10px;
}

.qa-description {
    color: #666;
    font-size: 14px;
    margin-bottom: 20px;
}

.ask-question-box {
    background: var(--primary-light);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    border: 1px solid var(--primary-color);
}

.ask-question-box h4 {
    color: var(--primary-dark);
    font-size: 18px;
    margin-bottom: 15px;
}

.btn-submit-question {
    background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-submit-question:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 191, 191, 0.3);
}

.questions-list {
    margin-top: 30px;
}

.questions-list h4 {
    font-size: 20px;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0e0e0;
}

.question-item {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border: 1px solid #e0e0e0;
}

.question-content {
    margin-bottom: 15px;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.question-header strong {
    color: #333;
    font-size: 15px;
}

.question-date {
    color: #999;
    font-size: 13px;
}

.question-text {
    color: #333;
    font-size: 15px;
    line-height: 1.6;
    padding: 10px;
    background: white;
    border-radius: 6px;
    border-left: 4px solid var(--primary-color);
}

.q-icon {
    color: var(--primary-color);
    font-weight: bold;
    font-size: 18px;
    margin-right: 8px;
}

.answers-list {
    margin-left: 30px;
    margin-top: 15px;
    border-left: 3px solid var(--primary-light);
    padding-left: 20px;
}

.answer-item {
    background: white;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border: 1px solid #e0e0e0;
    transition: all 0.3s;
}

.answer-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.shop-answer {
    background: linear-gradient(135deg, #fff8e1 0%, #fffaef 100%);
    border: 2px solid #ffd54f;
}

.answer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.answer-user {
    display: flex;
    align-items: center;
    gap: 10px;
}

.shop-badge {
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    color: white;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.answer-date {
    color: #999;
    font-size: 13px;
}

.answer-text {
    color: #333;
    font-size: 14px;
    line-height: 1.6;
    padding: 8px;
    background: rgba(15, 191, 191, 0.05);
    border-radius: 6px;
    border-left: 4px solid #4caf50;
}

.a-icon {
    color: #4caf50;
    font-weight: bold;
    font-size: 16px;
    margin-right: 8px;
}

.answer-votes {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.vote-btn {
    background: white;
    border: 2px solid #e0e0e0;
    padding: 6px 15px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 5px;
}

.vote-btn:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.vote-btn.upvote.active {
    background: linear-gradient(135deg, #4caf50, #66bb6a);
    color: white;
    border-color: #4caf50;
}

.vote-btn.downvote.active {
    background: linear-gradient(135deg, #f44336, #e57373);
    color: white;
    border-color: #f44336;
}

.vote-count {
    font-weight: 600;
}

.vote-count-readonly {
    font-size: 14px;
    color: #666;
    margin-right: 15px;
}

.reply-form-container {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px dashed #e0e0e0;
}

.btn-show-reply {
    background: var(--primary-light);
    color: var(--primary-dark);
    border: 2px solid var(--primary-color);
    padding: 8px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-show-reply:hover {
    background: var(--primary-color);
    color: white;
}

.reply-form {
    margin-top: 15px;
    padding: 15px;
    background: var(--primary-light);
    border-radius: 8px;
}

.btn-submit-answer {
    background: linear-gradient(180deg, #4caf50 0%, #45a049 100%);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.btn-submit-answer:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.no-questions {
    text-align: center;
    padding: 40px;
    color: #999;
}
</style>

<div class="container py-4">
  <!-- NÚT QUAY LẠI Ở TRÊN -->
  <div class="back-button-top">
    <a href="index.php?controller=category&action=index" class="btn-back-top">
      ← Quay lại danh sách
    </a>
  </div>

  <!-- THÔNG TIN SẢN PHẨM -->
  <div class="product-container">
    <div class="row">
      <!-- Cột ảnh -->
      <div class="col-md-5">
        <?php
          $mainImage = !empty($images) ? $images[0]['image_url'] : '';
          if (empty($mainImage)) {
              $mainImageSrc = 'https://via.placeholder.com/400x500?text=No+Image';
          } else {
              $mainImageSrc = $ASSET . '/' . $mainImage;
          }
        ?>
        <div class="main-image" id="mainImage">
          <img src="<?php echo htmlspecialchars($mainImageSrc); ?>" 
               alt="<?php echo htmlspecialchars($book['title']); ?>">
        </div>

        <?php if (!empty($images) && count($images) > 1): ?>
          <div class="thumb-images">
            <?php foreach ($images as $img): 
                $thumbSrc = $ASSET . '/' . $img['image_url'];
            ?>
              <div class="thumb-item" onclick="changeImage('<?php echo htmlspecialchars($thumbSrc); ?>')">
                <img src="<?php echo htmlspecialchars($thumbSrc); ?>" alt="thumb">
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Cột thông tin -->
      <div class="col-md-7">
        <h1 class="product-title">
          <?php echo htmlspecialchars($book['title']); ?>
        </h1>

        <?php if (!empty($book['rating_avg'])): ?>
          <div class="mt-2 mb-3">
            <span style="color: #ffc107; font-size: 20px;">
              <?php 
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= round($book['rating_avg'])) {
                        echo '★';
                    } else {
                        echo '☆';
                    }
                }
              ?>
            </span>
            <span class="text-muted" style="font-size: 15px;">
              <?php echo number_format($book['rating_avg'], 1); ?> 
              (<?php echo (int)$book['review_count']; ?> đánh giá)
            </span>
          </div>
        <?php endif; ?>

        <?php
          // Tính giá hiển thị
          $displayPrice = 0;
          $selectedVariantId = null;
          if (!empty($variants)) {
              foreach ($variants as $v) {
                  $p = !empty($v['sale_price']) ? $v['sale_price'] : $v['price'];
                  if (empty($p)) {
                      $p = 0;
                  }
                  if ($p > 0 && ($displayPrice == 0 || $p < $displayPrice)) {
                      $displayPrice = $p;
                      $selectedVariantId = $v['id'];
                  }
              }
          }
        ?>

        <div class="product-price" id="productPrice">
          <?php echo number_format($displayPrice); ?>₫
        </div>

        <!-- CHỌN BIẾN THỂ BÌA CỨNG / BÌA MỀM -->
        <?php if (!empty($variants)): ?>
          <div class="variant-selector">
            <strong style="display: block; margin-bottom: 15px; font-size: 16px; color: #333;">
              ✨ Chọn phiên bản:
            </strong>
            <div class="variant-buttons">
              <?php 
              $variantIndex = 0;
              foreach ($variants as $v): 
                  $vPrice = !empty($v['sale_price']) ? $v['sale_price'] : $v['price'];
                  $activeClass = ($variantIndex === 0) ? 'active' : '';
                  $variantIndex++;
              ?>
                <div class="variant-btn <?php echo $activeClass; ?>" 
                     data-id="<?php echo $v['id']; ?>"
                     data-price="<?php echo $vPrice; ?>"
                     onclick="selectVariant(this)">
                  <span class="format-name">📖 <?php echo htmlspecialchars($v['format']); ?></span>
                  <span class="format-price"><?php echo number_format($vPrice); ?>₫</span>
                  <span class="format-stock">Còn: <?php echo (int)$v['stock']; ?> sản phẩm</span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="description-box">
          <strong style="display: block; margin-bottom: 10px; color: var(--primary-color); font-size: 16px;">
            📝 Mô tả sản phẩm:
          </strong>
          <?php 
            $desc = !empty($book['description']) ? $book['description'] : 'Chưa có mô tả';
          ?>
          <p style="line-height: 1.6; color: #555; margin: 0;">
            <?php echo nl2br(htmlspecialchars($desc)); ?>
          </p>
        </div>

        <!-- CHỌN SỐ LƯỢNG -->
        <div class="quantity-selector">
          <strong style="display: block; margin-bottom: 15px; font-size: 16px; color: #333;">
            🔢 Số lượng:
          </strong>
          <div class="quantity-controls">
            <button type="button" class="qty-btn" onclick="decreaseQty()" id="decreaseBtn">−</button>
            <input type="number" id="quantityInput" value="1" min="1" max="100" readonly>
            <button type="button" class="qty-btn" onclick="increaseQty()" id="increaseBtn">+</button>
          </div>
          <p class="stock-info" id="stockInfo">
            📦 Còn lại: <span id="currentStock">0</span> sản phẩm
          </p>
        </div>

        <!-- NÚT THÊM GIỎ VÀ MUA NGAY -->
        <div class="action-buttons">
          <a href="#" 
             class="btn-add-cart" id="addToCartBtn"
             onclick="addToCart(event)">
            🛒 Thêm vào giỏ hàng
          </a>
          <a href="#" 
             class="btn-buy-now" id="buyNowBtn"
             onclick="buyNow(event)">
            ⚡ Mua ngay
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- PHẦN BÌNH LUẬN VÀ ĐÁNH GIÁ -->
  <div class="review-section">
    <h2 class="review-title">⭐ Đánh giá & Bình luận</h2>
    
    <!-- Thống kê đánh giá -->
    <?php if (!empty($commentStats) && $commentStats['total'] > 0): ?>
      <div class="review-summary">
        <span class="rating-big"><?php echo number_format($commentStats['avg_rating'], 1); ?></span>
        <div class="rating-stars-big">
          <?php 
            $avgRating = round($commentStats['avg_rating']);
            for ($i = 1; $i <= 5; $i++) {
                echo $i <= $avgRating ? '★' : '☆';
            }
          ?>
        </div>
        <p class="total-reviews"><?php echo (int)$commentStats['total']; ?> đánh giá</p>
      </div>
    <?php endif; ?>

    <!-- Form viết bình luận -->
    <?php if (!empty($_SESSION['user'])): ?>
      <?php if ($hasPurchased): ?>
        <!-- User đã mua, cho phép đánh giá -->
        <div class="write-review-box">
          <h4>✍️ Viết đánh giá của bạn</h4>
          <p class="verified-purchase">✅ Bạn đã mua sản phẩm này</p>
          <form action="index.php?controller=product&action=addComment" method="POST" id="reviewForm">
            <input type="hidden" name="book_id" value="<?php echo (int)$book['id']; ?>">
            <input type="hidden" name="rating" id="ratingValue" value="" required>
            
            <div class="form-group">
              <label>Đánh giá của bạn: <span id="ratingText" style="color: var(--primary-color); font-weight: bold;"></span></label>
              <div class="star-rating-input">
                <span class="star" data-rating="1" onclick="setRating(1)">★</span>
                <span class="star" data-rating="2" onclick="setRating(2)">★</span>
                <span class="star" data-rating="3" onclick="setRating(3)">★</span>
                <span class="star" data-rating="4" onclick="setRating(4)">★</span>
                <span class="star" data-rating="5" onclick="setRating(5)">★</span>
              </div>
              <p class="rating-hint">👆 Click vào sao để chọn điểm</p>
            </div>
            
            <div class="form-group">
              <label>Nội dung bình luận:</label>
              <textarea name="content" class="form-control" rows="4" 
                        placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." 
                        required></textarea>
            </div>
            
            <button type="submit" class="btn-submit-review">
              📝 Gửi đánh giá
            </button>
          </form>
        </div>
      <?php else: ?>
        <!-- User chưa mua -->
        <div class="purchase-required-box">
          <div class="purchase-required-content">
            <div class="icon">🛒</div>
            <h4>Bạn cần mua sản phẩm để đánh giá</h4>
            <p>Chỉ khách hàng đã mua và nhận sản phẩm mới có thể viết đánh giá để đảm bảo chất lượng đánh giá.</p>
            
            <!-- Form thêm vào giỏ hàng -->
            <form method="get" action="index.php" style="margin: 0;">
              <input type="hidden" name="controller" value="cart">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="id" value="<?php echo (int)$book['id']; ?>">
              <input type="hidden" name="redirect" value="cart">
              <button type="submit" class="btn-buy-now">
                🛒 Mua ngay
              </button>
            </form>
          </div>
        </div>
      <?php endif; ?>
    <?php else: ?>
    <div class="login-prompt">
      <p>Bạn cần <a href="index.php?controller=auth&action=login">đăng nhập</a> để viết đánh giá</p>
    </div>
    <?php endif; ?>

    <!-- Danh sách bình luận -->
    <div class="comments-list">
      <h4>💬 Bình luận từ khách hàng</h4>
      
      <?php if (!empty($comments) && count($comments) > 0): ?>
        <?php foreach ($comments as $comment): ?>
        <div class="comment-item">
          <div class="comment-header">
            <strong>👤 <?php echo htmlspecialchars($comment['user_name']); ?></strong>
            
            <?php if (!empty($comment['rating'])): ?>
            <span class="comment-rating">
              <?php 
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $comment['rating'] ? '★' : '☆';
                }
              ?>
            </span>
            <?php endif; ?>
          </div>
          
          <p class="comment-date">
            <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
          </p>
          
          <div class="comment-content">
            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-comments">
          <p>Chưa có bình luận nào cho sản phẩm này.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- PHẦN HỎI ĐÁP SẢN PHẨM -->
  <div class="qa-section">
    <h2 class="qa-title">❓ Hỏi đáp sản phẩm</h2>
    <p class="qa-description">Bạn có thắc mắc về sản phẩm? Đặt câu hỏi ngay để được giải đáp!</p>

    <!-- Form đặt câu hỏi -->
    <?php if (!empty($_SESSION['user'])): ?>
    <div class="ask-question-box">
      <h4>📝 Đặt câu hỏi của bạn</h4>
      <form id="askQuestionForm">
        <input type="hidden" name="book_id" value="<?php echo (int)$book['id']; ?>">
        <div class="form-group">
          <textarea name="question" id="questionInput" class="form-control" rows="3" 
                    placeholder="Nhập câu hỏi của bạn về sản phẩm..." 
                    required></textarea>
        </div>
        <button type="submit" class="btn-submit-question">
          💬 Gửi câu hỏi
        </button>
      </form>
    </div>
    <?php else: ?>
    <div class="login-prompt">
      <p>Bạn cần <a href="index.php?controller=auth&action=login">đăng nhập</a> để đặt câu hỏi</p>
    </div>
    <?php endif; ?>

    <!-- Danh sách câu hỏi -->
    <div class="questions-list">
      <h4>💬 Các câu hỏi từ khách hàng (<?php echo $totalQuestions; ?>)</h4>
      
      <?php if (!empty($questions) && count($questions) > 0): ?>
        <?php foreach ($questions as $question): ?>
        <div class="question-item">
          <!-- Câu hỏi -->
          <div class="question-content">
            <div class="question-header">
              <strong>👤 <?php echo htmlspecialchars($question['user_name']); ?></strong>
              <span class="question-date">
                <?php echo date('d/m/Y H:i', strtotime($question['created_at'])); ?>
              </span>
            </div>
            <div class="question-text">
              <span class="q-icon">Q:</span>
              <?php echo nl2br(htmlspecialchars($question['question'])); ?>
            </div>
          </div>

          <!-- Danh sách câu trả lời -->
          <?php if (!empty($question['answers'])): ?>
          <div class="answers-list">
            <?php foreach ($question['answers'] as $answer): ?>
            <div class="answer-item <?php echo $answer['is_shop_answer'] ? 'shop-answer' : ''; ?>">
              <div class="answer-header">
                <div class="answer-user">
                  <strong>👤 <?php echo htmlspecialchars($answer['user_name']); ?></strong>
                  <?php if ($answer['is_shop_answer']): ?>
                    <span class="shop-badge">🏪 Người bán</span>
                  <?php endif; ?>
                </div>
                <span class="answer-date">
                  <?php echo date('d/m/Y H:i', strtotime($answer['created_at'])); ?>
                </span>
              </div>
              <div class="answer-text">
                <span class="a-icon">A:</span>
                <?php echo nl2br(htmlspecialchars($answer['answer'])); ?>
              </div>
              
              <!-- Voting buttons -->
              <?php if (!empty($_SESSION['user'])): ?>
              <div class="answer-votes">
                <button class="vote-btn upvote <?php echo isset($answer['user_vote']) && $answer['user_vote'] === 'upvote' ? 'active' : ''; ?>" 
                        data-answer-id="<?php echo $answer['id']; ?>" 
                        data-vote-type="upvote">
                  👍 <span class="vote-count"><?php echo $answer['upvotes']; ?></span>
                </button>
                <button class="vote-btn downvote <?php echo isset($answer['user_vote']) && $answer['user_vote'] === 'downvote' ? 'active' : ''; ?>" 
                        data-answer-id="<?php echo $answer['id']; ?>" 
                        data-vote-type="downvote">
                  👎 <span class="vote-count"><?php echo $answer['downvotes']; ?></span>
                </button>
              </div>
              <?php else: ?>
              <div class="answer-votes">
                <span class="vote-count-readonly">👍 <?php echo $answer['upvotes']; ?></span>
                <span class="vote-count-readonly">👎 <?php echo $answer['downvotes']; ?></span>
              </div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <!-- Form trả lời (chỉ hiện khi đã đăng nhập) -->
          <?php if (!empty($_SESSION['user'])): ?>
          <div class="reply-form-container">
            <button class="btn-show-reply" onclick="toggleReplyForm(<?php echo $question['id']; ?>)">
              💬 Trả lời câu hỏi
            </button>
            <form class="reply-form" id="replyForm<?php echo $question['id']; ?>" style="display: none;">
              <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
              <div class="form-group">
                <textarea name="answer" class="form-control" rows="2" 
                          placeholder="Nhập câu trả lời của bạn..." 
                          required></textarea>
              </div>
              <button type="submit" class="btn-submit-answer">
                📤 Gửi trả lời
              </button>
            </form>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-questions">
          <p>Chưa có câu hỏi nào cho sản phẩm này. Hãy là người đầu tiên đặt câu hỏi!</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- SẢN PHẨM LIÊN QUAN -->
  <?php if (!empty($relatedProducts)): ?>
  <div class="related-section">
    <h2 class="related-title">📚 Sản phẩm liên quan</h2>
    <div class="row">
      <?php foreach ($relatedProducts as $product): ?>
        <div class="col-md-3 col-sm-6 mb-4">
          <div class="card h-100 shadow-sm product-card">
            
            <div class="card-img-container position-relative">
              <a href="index.php?controller=account&action=addWishlist&id=<?= (int)$product['id'] ?>" 
                 class="btn-wishlist-overlay" 
                 title="Thêm vào yêu thích">
                 ♥
              </a>
              <a href="index.php?controller=product&action=detail&id=<?= (int)$product['id'] ?>" class="d-block w-100 h-100">
                <?php
                  $imgSrc = !empty($product['image_url']) 
                      ? $ASSET . '/' . $product['image_url']
                      : 'https://via.placeholder.com/200x250?text=No+Image';
                ?>
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                     alt="<?php echo htmlspecialchars($product['title']); ?>">
              </a>
            </div>
            
            <div class="card-body d-flex flex-column pb-5">
              <p class="card-author text-muted small mb-1">Tác giả</p>
              <h6 class="card-title product-title mb-2">
                <a href="index.php?controller=product&action=detail&id=<?= (int)$product['id'] ?>">
                  <?php echo htmlspecialchars($product['title']); ?>
                </a>
              </h6>
              <div class="price-wrap mt-auto">
                <span class="product-price">
                  <?php echo number_format($product['display_price']); ?>₫
                </span>
              </div>
            </div>

            <div class="card-footer">
              <div class="d-flex gap-1">
                <form method="get" action="index.php" class="m-0 flex-grow-1">
                  <input type="hidden" name="controller" value="cart">
                  <input type="hidden" name="action" value="add">
                  <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-primary w-100" title="Thêm vào giỏ hàng">
                    + Giỏ
                  </button>
                </form>

                <a href="index.php?controller=product&action=detail&id=<?= (int)$product['id'] ?>" 
                   class="btn btn-sm btn-outline-dark flex-grow-1" title="Xem chi tiết">
                  Chi tiết
                </a>
              </div>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
// Hàm chọn rating sao
function setRating(rating) {
    // Lưu giá trị vào hidden input
    document.getElementById('ratingValue').value = rating;
    
    // Bỏ active của tất cả sao
    const stars = document.querySelectorAll('.star');
    stars.forEach(star => star.classList.remove('active'));
    
    // Thêm active cho các sao được chọn
    for (let i = 0; i < rating; i++) {
        stars[i].classList.add('active');
    }
    
    // Hiển thị text mô tả
    const ratingTexts = {
        1: '(1 sao - Không hài lòng)',
        2: '(2 sao - Chưa tốt lắm)',
        3: '(3 sao - Bình thường)',
        4: '(4 sao - Tốt)',
        5: '(5 sao - Tuyệt vời!)'
    };
    document.getElementById('ratingText').textContent = ratingTexts[rating];
}

// Validate form trước khi submit
document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
    const rating = document.getElementById('ratingValue').value;
    if (!rating) {
        e.preventDefault();
        alert('⚠️ Vui lòng chọn số sao đánh giá!');
        return false;
    }
});

// Đổi ảnh khi click thumbnail
function changeImage(newSrc) {
    document.querySelector('#mainImage img').src = newSrc;
}

// Chọn biến thể
let selectedVariantId = <?php echo !empty($selectedVariantId) ? $selectedVariantId : 0; ?>;
let maxStock = 0;

function selectVariant(element) {
    // Bỏ active của tất cả
    document.querySelectorAll('.variant-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    
    // Active nút được chọn
    element.classList.add('active');
    
    // Lấy thông tin
    selectedVariantId = element.getAttribute('data-id');
    const price = element.getAttribute('data-price');
    const stock = element.querySelector('.format-stock').textContent.match(/\d+/)[0];
    maxStock = parseInt(stock);
    
    // Cập nhật giá
    document.getElementById('productPrice').innerHTML = 
        new Intl.NumberFormat('vi-VN').format(price) + '₫';
    
    // Cập nhật thông tin stock
    document.getElementById('currentStock').textContent = maxStock;
    
    // Reset số lượng về 1
    document.getElementById('quantityInput').value = 1;
    updateQuantityButtons();
}

// Hàm tăng/giảm số lượng
function decreaseQty() {
    const input = document.getElementById('quantityInput');
    let value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
        updateQuantityButtons();
    }
}

function increaseQty() {
    const input = document.getElementById('quantityInput');
    let value = parseInt(input.value);
    if (value < maxStock && value < 100) {
        input.value = value + 1;
        updateQuantityButtons();
    }
}

function updateQuantityButtons() {
    const input = document.getElementById('quantityInput');
    const value = parseInt(input.value);
    
    // Disable nút giảm nếu = 1
    document.getElementById('decreaseBtn').disabled = (value <= 1);
    
    // Disable nút tăng nếu đạt max
    document.getElementById('increaseBtn').disabled = (value >= maxStock || value >= 100);
}

// Hàm thêm vào giỏ hàng
function addToCart(event) {
    event.preventDefault();
    const bookId = <?php echo (int)$book['id']; ?>;
    const quantity = document.getElementById('quantityInput').value;
    
    let url = 'index.php?controller=cart&action=add&id=' + bookId + '&quantity=' + quantity;
    if (selectedVariantId) {
        url += '&variant_id=' + selectedVariantId;
    }
    
    window.location.href = url;
}

// Hàm mua ngay
function buyNow(event) {
    event.preventDefault();
    const bookId = <?php echo (int)$book['id']; ?>;
    const quantity = document.getElementById('quantityInput').value;
    
    let url = 'index.php?controller=cart&action=add&id=' + bookId + '&quantity=' + quantity + '&buynow=1';
    if (selectedVariantId) {
        url += '&variant_id=' + selectedVariantId;
    }
    
    window.location.href = url;
}

// Khởi tạo khi load trang
window.addEventListener('DOMContentLoaded', function() {
    // Lấy stock từ variant đầu tiên (active)
    const firstVariant = document.querySelector('.variant-btn.active');
    if (firstVariant) {
        const stock = firstVariant.querySelector('.format-stock').textContent.match(/\d+/)[0];
        maxStock = parseInt(stock);
        document.getElementById('currentStock').textContent = maxStock;
        updateQuantityButtons();
    }
});

// ==================== Q&A FUNCTIONALITY ====================

// Toggle reply form
function toggleReplyForm(questionId) {
    const form = document.getElementById('replyForm' + questionId);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

// Handle ask question form
document.getElementById('askQuestionForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('index.php?controller=product&action=askQuestion', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload(); // Reload để hiển thị câu hỏi mới
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Có lỗi xảy ra!');
    });
});

// Handle answer forms
document.querySelectorAll('.reply-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('index.php?controller=product&action=answerQuestion', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload(); // Reload để hiển thị câu trả lời mới
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Có lỗi xảy ra!');
        });
    });
});

// Handle vote buttons
document.querySelectorAll('.vote-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const answerId = this.dataset.answerId;
        const voteType = this.dataset.voteType;
        
        const formData = new FormData();
        formData.append('answer_id', answerId);
        formData.append('vote_type', voteType);
        
        fetch('index.php?controller=product&action=voteAnswer', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật UI
                const upvoteBtn = document.querySelector(`.vote-btn.upvote[data-answer-id="${answerId}"]`);
                const downvoteBtn = document.querySelector(`.vote-btn.downvote[data-answer-id="${answerId}"]`);
                
                // Reset active state
                upvoteBtn.classList.remove('active');
                downvoteBtn.classList.remove('active');
                
                // Update counts
                upvoteBtn.querySelector('.vote-count').textContent = data.upvotes;
                downvoteBtn.querySelector('.vote-count').textContent = data.downvotes;
                
                // Set active state if vote was added or changed
                if (data.action === 'added' || data.action === 'changed') {
                    if (data.vote_type === 'upvote') {
                        upvoteBtn.classList.add('active');
                    } else {
                        downvoteBtn.classList.add('active');
                    }
                }
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Có lỗi xảy ra!');
        });
    });
});

</script>
