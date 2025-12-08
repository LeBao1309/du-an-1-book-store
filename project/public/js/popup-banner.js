/**
 * Popup Banner - Hiển thị banner quảng cáo khi vào trang
 */
document.addEventListener('DOMContentLoaded', function() {
    const popupBanner = document.getElementById('popupBanner');
    const closePopupBtn = document.getElementById('closePopup');
    const popupOverlay = document.querySelector('.popup-overlay');
    
    if (!popupBanner) return;
    
    // Kiểm tra đã đóng popup trong session chưa
    const popupClosed = sessionStorage.getItem('popupClosed');
    
    // Hiển thị popup sau 1 giây nếu chưa đóng
    if (!popupClosed) {
        setTimeout(function() {
            popupBanner.classList.add('show');
        }, 1000);
    }
    
    // Đóng popup khi click nút X
    if (closePopupBtn) {
        closePopupBtn.addEventListener('click', function() {
            popupBanner.classList.remove('show');
            sessionStorage.setItem('popupClosed', 'true');
        });
    }
    
    // Đóng popup khi click overlay (vùng tối bên ngoài)
    if (popupOverlay) {
        popupOverlay.addEventListener('click', function(e) {
            if (e.target === popupOverlay) {
                popupBanner.classList.remove('show');
                sessionStorage.setItem('popupClosed', 'true');
            }
        });
    }
});
