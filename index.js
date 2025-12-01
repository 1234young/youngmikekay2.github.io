
// SLIDER SCRIPT 

// Base path for images (auto-detects if deployed in subfolder)
const BASE_PATH = window.location.pathname.includes('work-out-planner') ? '/work-out-planner/' : '/';

let currentIndex = 0;

// Slide data
const slides = [
  {
    title: 'Expert Trainers',
    desc: 'Our certified trainers are dedicated to providing personalized coaching and support, ensuring you achieve your fitness goals effectively and safely.',
    img: `${BASE_PATH}images/trainee.webp`,
    buttonText: 'Meet Our Trainers',
    action: () => { window.location.href = 'trainers.php'; }
  },
  {
    title: 'State of Art-Equipment',
    desc: 'Train with the latest fitness technology and premium-grade equipment designed for performance, safety, and results. Every machine is optimized to help you move better and progress faster.',
    img: `${BASE_PATH}images/lift-weighing.webp`,
    buttonText: 'See Plans',
    action: () => { window.location.href = '#plans'; }
  },
  {
    title: 'Community Support',
    desc: 'Our vibrant community keeps you accountable and inspired. Exchange training tips, track progress together, and grow stronger with people who share your goals.',
    img: `${BASE_PATH}images/community-workout.webp`,
    buttonText: 'Join Events',
    action: () => { window.location.href = 'events.php'; }
  }
];

// DOM elements
let titleEl, descEl, imgEl, btnEl, prevBtn, nextBtn;

// Lazy-load helper
function lazyLoadImage(imgElement, src) {
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          imgElement.src = src;
          observer.unobserve(imgElement);
        }
      });
    });
    observer.observe(imgElement);
  } else {
    // Fallback
    imgElement.src = src;
  }
}

// Initialize slider after DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  titleEl = document.getElementById('slide-title');
  descEl = document.getElementById('slide-desc');
  imgEl = document.getElementById('slide-img');
  btnEl = document.getElementById('slide-btn');
  prevBtn = document.querySelector('.prev');
  nextBtn = document.querySelector('.next');

  if (!slides || slides.length === 0) {
    console.warn('No slides to initialize');
    return;
  }

  // Event listeners for slider navigation
  if (prevBtn) prevBtn.addEventListener('click', () => changeSlide(currentIndex - 1));
  if (nextBtn) nextBtn.addEventListener('click', () => changeSlide(currentIndex + 1));

  // Extra buttons
  const bookBtn = document.getElementById("bookNow");
  if (bookBtn) bookBtn.addEventListener("click", () => { window.location.href = "booking.html"; });

  const joinBtn = document.getElementById("joinNow");
  if (joinBtn) joinBtn.addEventListener("click", () => { window.location.href = "signup.html"; });

  const planBtns = document.querySelectorAll(".plan-btn");
  planBtns.forEach(button => {
    button.addEventListener("click", e => {
      const plan = e.target.dataset.plan;
      window.location.href = `checkout.html?plan=${plan}`;
    });
  });

  // Initialize first slide
  changeSlide(0);

  // Expose for debugging or external calls
  window.changeSlide = changeSlide;
  window.currentIndex = currentIndex;
});

// Change slide function with lazy-loading
function changeSlide(index) {
  if (!slides || slides.length === 0) return;

  // Wrap index
  if (index < 0) index = slides.length - 1;
  if (index >= slides.length) index = 0;
  currentIndex = index;

  // Check DOM elements
  if (!titleEl || !descEl || !imgEl || !btnEl) return;

  // Fade out
  titleEl.style.opacity = 0;
  descEl.style.opacity = 0;
  imgEl.style.opacity = 0;
  btnEl.style.opacity = 0;

  setTimeout(() => {
    // Update content
    titleEl.textContent = slides[index].title;
    descEl.textContent = slides[index].desc;
    btnEl.textContent = slides[index].buttonText;
    btnEl.onclick = slides[index].action;

    // Lazy-load image
    lazyLoadImage(imgEl, slides[index].img);
    imgEl.alt = slides[index].title;

    // Fade in
    titleEl.style.opacity = 1;
    descEl.style.opacity = 1;
    imgEl.style.opacity = 1;
    btnEl.style.opacity = 1;
  }, 300);
}

// Responsive Menu Toggle
document.addEventListener("DOMContentLoaded", () => {
  const nav = document.querySelector(".nav-main");
  if (!nav) return;

  const menuToggle = document.createElement("div");
  menuToggle.classList.add("menu-toggle");
  menuToggle.innerHTML = `<div></div><div></div><div></div>`;
  nav.parentNode.insertBefore(menuToggle, nav.nextSibling);

  const navLinks = document.querySelector(".nav-left ul");
  if (!navLinks) return;

  // Toggle menu open/close
  menuToggle.addEventListener("click", () => {
    navLinks.classList.toggle("active");
    menuToggle.classList.toggle("active");
  });

  // Close menu on link click
  navLinks.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", () => {
      navLinks.classList.remove("active");
      menuToggle.classList.remove("active");
    });
  });
});

// TOAST SYSTEM
document.addEventListener("DOMContentLoaded", () => {
  function showToast(message, type = "success") {
    const container = document.getElementById("toast-container");
    if (!container) return;

    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = "0";
      setTimeout(() => toast.remove(), 500);
    }, 4000);
  }

  const params = new URLSearchParams(window.location.search);

  if (params.has("success")) showToast("🎉 Registration successful! Please login to continue.", "success");
  if (params.has("login")) showToast("✅ Login successful!", "success");
  if (params.has("error")) {
    if (params.get("error") === "invalid_login") showToast("Invalid email or password.", "error");
    if (params.get("error") === "email_exists") showToast("That email already exists. Try logging in.", "error");
  }

  if (window.history.replaceState) {
    const cleanUrl = window.location.origin + window.location.pathname;
    window.history.replaceState({}, document.title, cleanUrl);
  }
});
