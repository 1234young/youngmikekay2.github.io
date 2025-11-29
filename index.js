
let currentIndex = 0;

const slides = [
  {
    title: 'Expert Trainers',
    desc: '  Our certified trainers are dedicated to providing personalized coaching and support, ensuring you achieve your fitness goals effectively and safely.',
    img: '/work-out-planner/images/trainee.webp',        
    buttonText: 'Meet Our Trainers',
    action: () => { window.location.href = 'trainers.php'; }
  },
  {
    title: 'State of Art-Equipment',
    desc: 'Train with the latest fitness technology and premium-grade equipment designed for performance, safety, and results. Every machine is optimized to help you move better and progress faster.',
    img: '/work-out-planner/images/lift-weighing.webp',
    buttonText: 'See Plans',
    action: () => { window.location.href = '#plans'; }
  },
  {
    title: 'Community Support',
    desc: 'Our vibrant community keeps you accountable and inspired. Exchange training tips, track progress together, and grow stronger with people who share your goals.',
    img: '/work-out-planner/images/community-workout.webp',
    buttonText: 'Join Events',
    action: () => { window.location.href = 'events.php'; }
  }
];



// DOM elements (initialized after DOM ready)
let titleEl, descEl, imgEl, btnEl;
let prevBtn, nextBtn;



// Change slide with animation
function changeSlide(index) {
  if (typeof slides === 'undefined' || !Array.isArray(slides) || slides.length === 0) {
    console.warn('changeSlide: slides not defined or empty');
    return;
  }

  // ensure DOM nodes exist
  if (!titleEl || !descEl || !imgEl || !btnEl) {
    console.warn('changeSlide: DOM elements not available yet');
    return;
  }

  // Wrap index
  if (index < 0) index = slides.length - 1;
  if (index >= slides.length) index = 0;

  // Animate out
  titleEl.style.opacity = 0;
  descEl.style.opacity = 0;
  imgEl.style.opacity = 0;
  btnEl.style.opacity = 0;

  setTimeout(() => {
    // Update content
    titleEl.textContent = slides[index].title;
    descEl.textContent = slides[index].desc;

    // lazy-activate or set src
    if (imgEl.hasAttribute && imgEl.hasAttribute('data-src')) {
      imgEl.setAttribute('data-src', slides[index].img);
      lazyActivate(imgEl);
    } else {
      imgEl.src = slides[index].img;
    }

    btnEl.textContent = slides[index].buttonText;
    btnEl.onclick = slides[index].action;

    // Animate back in
    titleEl.style.opacity = 1;
    descEl.style.opacity = 1;
    imgEl.style.opacity = 1;
    btnEl.style.opacity = 1;

    currentIndex = index;
    window.currentIndex = currentIndex;
  }, 400);
}

// initialize after DOM ready
document.addEventListener('DOMContentLoaded', () => {
  // bind DOM elements now
  titleEl = document.getElementById("slide-title");
  descEl = document.getElementById("slide-desc");
  imgEl = document.getElementById("slide-img");
  btnEl = document.getElementById("slide-btn");

  prevBtn = document.querySelector(".prev");
  nextBtn = document.querySelector(".next");

  // attach listeners with null checks
  if (prevBtn) prevBtn.addEventListener("click", () => changeSlide(currentIndex - 1));
  if (nextBtn) nextBtn.addEventListener("click", () => changeSlide(currentIndex + 1));

  // init lazy loader, preloads and hero bg
  initLazyObserver();
  preloadCritical();
  lazyLoadHeroBg();

  // Init first slide if slides exist
  if (typeof slides !== 'undefined' && Array.isArray(slides) && slides.length > 0) {
    changeSlide(0);
  } else {
    console.warn('No slides to initialize');
  }

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

  // expose for debugging / external calls
  window.changeSlide = changeSlide;
  window.currentIndex = currentIndex;
});

// Responsive Menu Toggle
document.addEventListener("DOMContentLoaded", () => {
  const menuToggle = document.createElement("div");
  menuToggle.classList.add("menu-toggle");
  menuToggle.innerHTML = `
    <div></div>
    <div></div>
    <div></div>
  `;

  // Append toggle button next to nav
  const nav = document.querySelector(".nav-main");
  nav.parentNode.insertBefore(menuToggle, nav.nextSibling);

  const navLinks = document.querySelector(".nav-left ul");

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

//TOAST SYSTEM (Bottom Right
document.addEventListener("DOMContentLoaded", () => {

  function showToast(message, type = "success") {
    const container = document.getElementById("toast-container");
    if (!container) return;

    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.textContent = message;

    container.appendChild(toast);

    // Auto remove after 4 seconds
    setTimeout(() => {
      toast.style.opacity = "0";
      setTimeout(() => toast.remove(), 500);
    }, 4000);
  }

  //READ PHP QUERY PARAMETERS FOR TOAST
  const params = new URLSearchParams(window.location.search);

  if (params.has("success")) {
    showToast("🎉 Registration successful! Please login to continue.", "success");
  }

  if (params.has("login")) {
    showToast("✅ Login successful!", "success");
  }

  if (params.has("error")) {
    if (params.get("error") === "invalid_login") {
      showToast(" Invalid email or password.", "error");
    }
    if (params.get("error") === "email_exists") {
      showToast(" That email already exists. Try logging in.", "error");
    }
  }
  //  Remove query parameters from URL so toast won't repeat on refresh
  if (window.history.replaceState) {
    const cleanUrl = window.location.origin + window.location.pathname;
    window.history.replaceState({}, document.title, cleanUrl);
  }

});
 