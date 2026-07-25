document.addEventListener('DOMContentLoaded', () => {
  // Common Elements
  const header = document.querySelector('.header');
  const menuToggleBtn = document.getElementById('menu-toggle-btn');
  const navLinks = document.querySelectorAll('.nav-link');

  // Mobile navigation toggle (if exists)
  if (menuToggleBtn && header) {
    menuToggleBtn.addEventListener('click', () => {
      header.classList.toggle('nav-active');
    });
  }

  // Close mobile nav when clicking a link
  if (navLinks) {
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        header.classList.remove('nav-active');
      });
    });
  }

  /* ==========================================================================
     LANDING PAGE LOGIC
     ========================================================================== */
  const cards = document.querySelectorAll('.product-card');
  const prevBtn = document.getElementById('prev-btn');
  const nextBtn = document.getElementById('next-btn');
  const progressFill = document.getElementById('progress-fill');
  const currentSlideNum = document.getElementById('current-slide-num');
  const heroBgImg = document.getElementById('hero-bg-img');

  // Check if we are on the landing page
  const isLandingPage = cards.length > 0 && heroBgImg;

  if (isLandingPage) {
    // Slide Data
    const slides = {
      1: {
        heroImage: 'assets/hero.jpg',
        slideNum: '01'
      },
      2: {
        heroImage: 'assets/hero_gold.jpg',
        slideNum: '02'
      }
    };
    
    let currentActiveIndex = 1;
    const totalSlides = Object.keys(slides).length;

    // Update Slide UI
    const updateSlide = (index) => {
      if (index < 1 || index > totalSlides) return;
      
      currentActiveIndex = index;
      
      // Update active card class
      cards.forEach(card => {
        const cardIndex = parseInt(card.getAttribute('data-index'), 10);
        if (cardIndex === currentActiveIndex) {
          card.classList.add('active');
        } else {
          card.classList.remove('active');
        }
      });

      // Update progress bar
      if (progressFill) {
        const percentage = (currentActiveIndex / totalSlides) * 100;
        progressFill.style.width = `${percentage}%`;
      }

      // Update pagination number
      if (currentSlideNum) {
        currentSlideNum.textContent = slides[currentActiveIndex].slideNum;
      }

      // Fade out hero image, change source, and fade back in
      heroBgImg.style.opacity = '0';
      setTimeout(() => {
        heroBgImg.src = slides[currentActiveIndex].heroImage;
        heroBgImg.onload = () => {
          heroBgImg.style.opacity = '1';
        };
      }, 400);
    };

    // Next / Prev actions
    const nextSlide = () => {
      let nextIndex = currentActiveIndex + 1;
      if (nextIndex > totalSlides) {
        nextIndex = 1;
      }
      updateSlide(nextIndex);
    };

    const prevSlide = () => {
      let prevIndex = currentActiveIndex - 1;
      if (prevIndex < 1) {
        prevIndex = totalSlides;
      }
      updateSlide(prevIndex);
    };

    // Event Listeners for buttons
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);

    // Click on cards directly to switch
    cards.forEach(card => {
      card.addEventListener('click', () => {
        const index = parseInt(card.getAttribute('data-index'), 10);
        if (index !== currentActiveIndex) {
          updateSlide(index);
        }
      });
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') {
        nextSlide();
      } else if (e.key === 'ArrowLeft') {
        prevSlide();
      }
    });

    // Initialize view
    updateSlide(1);
  }

  /* ==========================================================================
     LOGIN PAGE LOGIC
     ========================================================================== */
  const loginForm = document.getElementById('login-form-element');

  if (loginForm) {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordToggleBtn = document.getElementById('password-toggle-btn');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');

    // Toggle password visibility
    if (passwordToggleBtn && passwordInput) {
      passwordToggleBtn.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle active styling / change SVG visually
        passwordToggleBtn.classList.toggle('active');
        if (passwordToggleBtn.classList.contains('active')) {
          // Eye closed SVG
          passwordToggleBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
          `;
        } else {
          // Eye open SVG
          passwordToggleBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
          `;
        }
      });
    }

    // Helper functions for showing/clearing error states
    const showError = (input, errorElement, message) => {
      errorElement.textContent = message;
      errorElement.style.display = 'block';
      input.style.borderBottomColor = '#ff5e5e';
      input.style.boxShadow = '0 1px 0 #ff5e5e';
    };

    const clearError = (input, errorElement) => {
      errorElement.textContent = '';
      errorElement.style.display = 'none';
      input.style.borderBottomColor = '';
      input.style.boxShadow = '';
    };

    const validateEmail = (email) => {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(String(email).toLowerCase());
    };

    // Form submission validation
    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();
      let isValid = true;

      // Email validation
      if (!emailInput.value.trim()) {
        showError(emailInput, emailError, 'Email address is required');
        isValid = false;
      } else if (!validateEmail(emailInput.value)) {
        showError(emailInput, emailError, 'Please enter a valid email address');
        isValid = false;
      } else {
        clearError(emailInput, emailError);
      }

      // Password validation
      if (!passwordInput.value) {
        showError(passwordInput, passwordError, 'Password is required');
        isValid = false;
      } else if (passwordInput.value.length < 6) {
        showError(passwordInput, passwordError, 'Password must be at least 6 characters');
        isValid = false;
      } else {
        clearError(passwordInput, passwordError);
      }

      if (isValid) {
        // Client-side validation passed - let the form really submit to login.php
        const submitBtn = loginForm.querySelector('.submit-btn');
        submitBtn.innerHTML = '<span>Signing In...</span>';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';
        loginForm.submit();
      }
    });

    // Real-time error clearing
    emailInput.addEventListener('input', () => {
      if (emailInput.value.trim() && validateEmail(emailInput.value)) {
        clearError(emailInput, emailError);
      }
    });

    passwordInput.addEventListener('input', () => {
      if (passwordInput.value.length >= 6) {
        clearError(passwordInput, passwordError);
      }
    });
  }

  /* ==========================================================================
     COLLECTION PAGE FILTER LOGIC
     ========================================================================== */
  const filterTabs = document.querySelectorAll('.filter-tab');
  const productCards = document.querySelectorAll('.product-item-card');
  const visibleCount = document.getElementById('visible-count');

  if (filterTabs.length > 0 && productCards.length > 0) {
    filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        // Remove active class from all tabs
        filterTabs.forEach(t => t.classList.remove('active'));
        // Add active class to clicked tab
        tab.classList.add('active');

        const filterValue = tab.getAttribute('data-filter');
        let count = 0;

        productCards.forEach(card => {
          const cardCategory = card.getAttribute('data-category');
          
          if (filterValue === 'all' || cardCategory === filterValue) {
            // Show with fade transition
            card.classList.remove('hide');
            // Subtle animate in
            card.style.opacity = '0';
            card.style.transform = 'translateY(15px) scale(0.98)';
            setTimeout(() => {
              card.style.opacity = '1';
              card.style.transform = 'translateY(0) scale(1)';
            }, 50);
            count++;
          } else {
            // Hide card
            card.classList.add('hide');
          }
        });

        // Update count text
        if (visibleCount) {
          visibleCount.textContent = count;
        }
      });
    });
  }

  /* ==========================================================================
     ADD TO CART (talks to the PHP/MySQL backed cart)
     Handles the hero "Add to Cart" button (index.php) and the
     "Add to Bag" overlay buttons on the collection grid (collection.php)
     ========================================================================== */
  function updateHeaderCartCount(count) {
    const badge = document.getElementById('cart-count');
    if (badge) badge.textContent = count;
  }

  function postToCart(productId, button, defaultLabel) {
    const formData = new URLSearchParams();
    formData.append('product_id', productId);
    formData.append('quantity', 1);

    fetch('cart_add.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString(),
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          updateHeaderCartCount(data.cart_count);
          if (button) {
            const original = button.innerHTML;
            button.innerHTML = button.tagName === 'BUTTON' && button.querySelector('span') ? '<span>Added ✓</span>' : 'Added ✓';
            setTimeout(() => { button.innerHTML = original; }, 1200);
          }
        }
      })
      .catch(() => {});
  }

  document.addEventListener('click', (e) => {
    const cartBtn = e.target.closest('.add-to-cart-btn');
    if (cartBtn && cartBtn.dataset.id) {
      e.preventDefault();
      postToCart(cartBtn.dataset.id, cartBtn);
    }

    const bagBtn = e.target.closest('.add-to-bag-overlay');
    if (bagBtn && bagBtn.dataset.id) {
      e.preventDefault();
      postToCart(bagBtn.dataset.id, bagBtn);
    }
  });
});

