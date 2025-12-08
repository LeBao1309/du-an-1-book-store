/**
 * Category Slider - Horizontal navigation for categories
 * Shows 5 items per row with prev/next navigation
 */
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.categories-slider');
    const prevBtn = document.querySelector('.cat-nav-btn.prev');
    const nextBtn = document.querySelector('.cat-nav-btn.next');
    
    if (!slider || !prevBtn || !nextBtn) return;
    
    const items = slider.querySelectorAll('.category-card');
    const totalItems = items.length;
    
    // Configuration
    let itemsPerView = 5;
    let currentIndex = 0;
    
    // Update items per view based on screen size
    function updateItemsPerView() {
        const width = window.innerWidth;
        if (width <= 768) {
            itemsPerView = 2;
        } else if (width <= 1200) {
            itemsPerView = 3;
        } else {
            itemsPerView = 5;
        }
        updateSlider();
    }
    
    // Calculate and apply scroll position
    function updateSlider() {
        if (totalItems === 0) return;
        
        const itemWidth = slider.scrollWidth / totalItems;
        const scrollPosition = currentIndex * itemWidth;
        
        slider.scrollTo({
            left: scrollPosition,
            behavior: 'smooth'
        });
        
        updateButtons();
    }
    
    // Show/hide buttons based on position
    function updateButtons() {
        // Hide buttons if total items <= items per view
        if (totalItems <= itemsPerView) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            return;
        }
        
        prevBtn.style.display = 'flex';
        nextBtn.style.display = 'flex';
        
        // Disable prev button at start
        if (currentIndex === 0) {
            prevBtn.disabled = true;
            prevBtn.style.opacity = '0.3';
        } else {
            prevBtn.disabled = false;
            prevBtn.style.opacity = '1';
        }
        
        // Disable next button at end
        if (currentIndex >= totalItems - itemsPerView) {
            nextBtn.disabled = true;
            nextBtn.style.opacity = '0.3';
        } else {
            nextBtn.disabled = false;
            nextBtn.style.opacity = '1';
        }
    }
    
    // Event listeners
    prevBtn.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlider();
        }
    });
    
    nextBtn.addEventListener('click', function() {
        if (currentIndex < totalItems - itemsPerView) {
            currentIndex++;
            updateSlider();
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        updateItemsPerView();
    });
    
    // Initialize
    updateItemsPerView();
});
