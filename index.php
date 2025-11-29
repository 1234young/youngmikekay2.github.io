<?php 
 // Detect if HTTPS is being used
 $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;

 session_start([
    'cookie_httponly' => true,       // Prevents JS access to session cookie
    'cookie_secure' => $isSecure,    // Only send cookie over HTTPS
    'cookie_samesite' => 'Strict',   // Prevent CSRF
    'use_strict_mode' => true,       // Prevent session fixation
    'use_only_cookies' => true       // Don't allow URL-based session IDs
 ]);

 // Optional: regenerate session ID on new session for extra security
 if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
  }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>workout.dev</title>
   <script src="https://kit.fontawesome.com/f16f489acd.js" crossorigin="anonymous"></script>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- Preload the CSS early -->
  <link rel="preload" href="styles.css?v=2" as="style">
  <link   rel="stylesheet" href="styles.css?v=2">
</head>
<body>

  <?php
  // Toast message logic
  $toastMessage = '';
  $toastType = '';

  if (isset($_GET['success'])) {
    $toastMessage = '🎉 Registration successful! You can now log in.';
    $toastType = 'success';
  } elseif (isset($_GET['error']) && $_GET['error'] === 'email_exists') {
    $toastMessage = '⚠️ That email is already registered. Please log in instead.';
    $toastType = 'error';
  } elseif (isset($_GET['error']) && $_GET['error'] === 'invalid_login') {
    $toastMessage = '❌ Invalid email or password.';
    $toastType = 'error';
  }
  elseif (isset($_GET['login']) && $_GET['login'] === 'success') {
  $toastMessage = '✅ Login successful! Welcome back.';
  $toastType = 'success';
  }


  if ($toastMessage !== '') {
    echo "<div class='toast $toastType'>$toastMessage</div>";
  }
  ?>


  <!-- Header -->
  <header>
    <div class="container nav">
      <div class="brand">
        <picture>
          <source srcset="images/fitnessfirst.webp" type="image/webp">
          <img src="images/fitnessfirst.jpg" width="50px" height="50px" alt="Workout-Logo" loading="eager" decoding="async">
        </picture>
        <div class="brand-text">
          <div class="brand-title">Workout Planner</div>
          <div class="brand-sub">Train Your Best</div>
        </div>
      </div>
        <div class="mid-welcome">
          <h1>Welcome</h1>
        </div> 
        

      <div class="nav-main">
        <nav class="nav-left" aria-label="Primary" id=""primary-nav>
          <ul>
            <li><a href="#" id="login-link">Login</a></li>
            <li><a href="#" id="register-link">Register</a></li>
            <li><a href="#home">Home</a></li>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="admin/admin_dashboard.php" id="admin-link">Admin Page</a></li>
            <?php endif; ?>
            
            <li><a href="#plans">Classes</a></li>
            <li><a href="#blogs">Blog</a></li>
            <li><a href="#contact">Contact Us</a></li>
          </ul>
        </nav>  
    
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero" id="home">
    <div class="hero-overlay container">
      <p class="hero-text">
        Join us at Workout.dev, where we empower you to reach your health and fitness goals with a state-of-the-art facility and a supportive community.
      </p>
      <h1>Elevate Your Fitness Journey</h1>
      <a class="book" id="bookNow" href="#">Book Now</a>
    </div>
  </section>

  <!-- Slider Section -->
  <section class="slider">
    <!-- Left content -->
    <div class="slider-content">
      <h1 id="slide-title">Expert Trainers</h1>
      <p id="slide-desc">
        Our certified trainers are dedicated to providing personalized coaching and support,
        ensuring you achieve your fitness goals effectively and safely.
      </p>
     <button id="slide-btn" onclick="window.location.href='trainers.php'">
      Meet Our Trainers
     </button>
    </div>

    <!-- Right image -->
    <div class="slider-image">
      <picture>
        <img id="slide-img" src="images/trainee.webp" alt="Slide Image" width="600" height="250" loading="lazy">
      </picture>
    </div>

   <!-- Navigation arrows -->
    <button class="prev">&#10094;</button>
    <button class="next">&#10095;</button>
  </section>


  <!-- Join Now Section -->
  <section class="feature" id="about">
    <div class="feature-card container">
      <h1>Why Workout Planner?</h1>
      <p class="muted">Powered by smart technology, Workout Planner turns your fitness journey into a structured, optimized experience.<br>
         From adaptive schedules to progress analytics, everything is designed to maximize results with minimal time wasted. It’s more than a planner it’s a personal trainer in your pocket.
         We make fitness approachable clear plans, friendly coaching and classes that fit your life.</p>

      <div class="media">
        <picture>
         <source srcset="images/workout-trainer.webp" type="image/webp">
         <img src="images/workout-trainer.webp" alt="coaches" loading="lazy" width="580" height="260">
        </picture>
        
        <div class="meta">
          <h2>Personalized Coaching</h2>
          <p class="muted">Our coaches create plans tailored to your goals and schedule.</p>
        </div>
        <div class="meta-action">
          <button class="join" id="openJoinModal">Join Now</button>
        </div>
      </div>
    </div>
  </section>

  
 <!-- Join Form Popup -->
 <div id="joinFormModal" class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close-btn" id="closeModal">&times;</span>
    <h2>Join Workout Planner</h2>
    <p class="muted">Fill out your details to start your personalized fitness journey.</p>

    <form id="joinForm">
      <label for="fullname">Full Name</label>
      <input type="text" id="fullname" name="fullname" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>

      <label for="phone">Phone Number</label>
      <input type="tel" id="phone" name="phone" required>

      <label for="location">Location</label>
      <input type="text" id="location" name="location" required>

      <label for="goals">Fitness Goals</label>
      <textarea id="goals" name="goals" required></textarea>

      <label for="sessionType">Preferred Session Type</label>
      <select id="sessionType" name="sessionType" required>
        <option value="">Select session type</option>
        <option value="physical">Physical</option>
        <option value="virtual">Virtual</option>
      </select>

      <button type="submit" class="submit-btn">Join Now</button>
    </form>
  </div>
 </div>

  <!-- Membership Plans -->
  <section class="plans" id="plans">
    <div class="container">
      <h1 class="centre">Membership Plans</h1>
      <p class="muted center">Simple, transparent pricing. Cancel anytime.</p>

      <div class="plans-grid">
        <div class="plan" data-plan="Basic">
          <h3>Basic</h3>
          <div class="price">$9<span>/mo</span></div>
          <ul>
            <li>Access to 20+ classes</li>
            <li>Community chat</li>
            <li>Monthly challenges</li>
          </ul>
          <button class="plan-btn" data-plan="Basic">Choose Basic</button>
        </div>

        <div class="plan highlight" data-plan="Premium">
          <h3>Premium</h3>
          <div class="price">$29<span>/mo</span></div>
          <ul>
            <li>All Basic features</li>
            <li>1-on-1 monthly check-in</li>
            <li>Priority booking</li>
          </ul>
          <button class="plan-btn" data-plan="Premium">Choose Premium</button>
        </div>

        <div class="plan" data-plan="Family">
          <h3>Family</h3>
          <div class="price">$49<span>/mo</span></div>
          <ul>
            <li>All Premium features</li>
            <li>Up to 4 members</li>
            <li>Family challenges</li>
          </ul>
          <button class="plan-btn" data-plan="Family">Choose Family</button>
        </div>
      </div>
    </div>
  </section>


   <!-- Blog  -->
   <section class="blog" id="blogs">
    <div class="container">
      <h2>From Our Blog</h2>
      <p class="muted">Latest tips, stories and updates from Workout Planner.</p>

      <div class="blog-grid">
        <article class="blog-card">
          <picture>
             <source srcset="images/hiit-girl.webp" type="image/webp">
            <img src="images/hiit-girl.jpg" alt="HIIT workout" class="blog-img" loading="lazy">
          </picture>

          <div class="blog-meta">
            <time datetime="2025-10-01">Oct 1, 2025</time>
            <h3 class="blog-title">5 HIIT Moves to Boost Your Cardio</h3>
            <p class="blog-excerpt">Quick, effective HIIT routines you can do at home to increase stamina and burn fat.</p>
            <a class="btn" href="blog/hiit.html">Read more</a>
          </div>
        </article>

        <article class="blog-card">
          <picture>
             <source srcset="images/nutrition-basics.webp" type="image/webp">
            <img src="images/nutrition-basics.jpg" alt="Nutrition tips" class="blog-img" loading="lazy">
          </picture>

          <div class="blog-meta">
            <time datetime="2025-09-18">Sep 18, 2025</time>
            <h3 class="blog-title">Nutrition Basics for Better Results</h3>
            <p class="blog-excerpt">Simple meal and timing tips to fuel your workouts and recovery.</p>
            <a class="btn" href="blog/nutrition.html">Read more</a>
          </div>
        </article>

        <article class="blog-card">
          <picture>
             <source srcset="images/best-cobrapose.webp" type="image/webp">
            <img src="images/best-cobrapose.jpg" alt="Blood Circulation" class="blog-img" loading="lazy">
          </picture>

          <div class="blog-meta">
            <time datetime="2025-09-05">Sep 5, 2025</time>
            <h3 class="blog-title">6 Moves to Boost Your Blood Circulation to Vital Organs</h3>
            <p class="blog-excerpt">How to improve blood flow to your vital organs and practices to complement your routine to stay consistent and avoid organ failure.</p>
            <a class="btn" href="blog/circulation.html">Read more</a>
          </div>
        </article>

        <article class="blog-card">
          <picture>
             <source srcset="images/recovery-technique.webp" type="image/webp">
             <img src="images/recovery-technique.jpg" alt="Yoga" class="blog-img" loading="lazy" >
          </picture>
          <div class="blog-meta">
            <time datetime="2025-09-05">Sep 5, 2025</time>
            <h3 class="blog-title">Recovery:Sleep, Rest and Mobility.</h3>
            <p class="blog-excerpt">Recovery thrives on quality sleep, intentional rest, and gentle mobility that rejuvenate both body and mind.
               Prioritizing these elements restores energy, prevents burnout, and empowers peak performance every day.
               How to add practices to your routine to stay consistent and avoid injuries.
            </p>
            <a class="btn" href="blog/recovery.html">Read more</a>
          </div>
        </article>
        
        <article class="blog-card">
          <picture>
             <source srcset="images/upperbody.webp" type="image/webp">
            <img src="images/upperbody.jpg" alt="Push-up" class="blog-img" loading="lazy">
          </picture>
          <div class="blog-meta">
            <time datetime="2025-09-05">Sep 5, 2025</time>
            <h3 class="blog-title">Upper Body Exercises: Workouts to boost physical strength.</h3>
            <p class="blog-excerpt">How to train your upper body to stay consistent and boost your upper body muscles and gain physical strength.</p>
            <a class="btn" href="blog/upperbody.html">Read more</a>
          </div>
        </article>
         
        <article class="blog-card">
          <picture>
             <source srcset="images/health.webp" type="image/webp">
            <img src="images/health.jpg" alt="Health" class="blog-img" loading="lazy">
          </picture>
          <div class="blog-meta">
            <time datetime="2025-09-05">Sep 5, 2025</time>
            <h3 class="blog-title">Healthy Tips: Adopt balanced habits, stay active, nourish your body, and practice self-love.</h3>
            <p class="blog-excerpt">Healthy living is a lifelong commitment to balance, nourishing both body and mind through mindful choices and movement. 
              Embrace wellness as self-respect, your body is a masterpiece worthy of care and consistency.
            </p>
            <a class="btn" href="blog/healthtips.html">Read more</a>
          </div>
        </article>

      </div>
    </div>
   </section>


   <!-- Contact  -->
   <section class="contact" id="contact">
    <div class="container-contact">
      <h2>Contact Us</h2>
      <p ><i class="fa-solid fa-paper-plane" id="fa-paper-plane"></i> workoutplanner@gmail.com   <i class="fa-solid fa-phone" id="fa-phone"></i>+254 757813837</p>
     
      <form class="contact-form" action="handlers/contact_handler.php" method="POST" >
        <input type="text" name="name" placeholder="Your Name" required />
        <input type="email" name="email" placeholder="Your Email" required />
        <textarea name="message_body" row="5" placeholder="Your Message" required></textarea>
        <button type="submit" >Send Message</button>
        <a class="logout" href="handlers/logout.php">Logout</a>
      </form>
    </div>
   </section>

  
   <!-- Footer -->
   <footer>
    <div class="container foot-wrap">
      <div>
        <strong>Workout Planner</strong>
        <div class="muted small">Move better. Live stronger.</div>
      </div>
      <div class="foot-links">
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
      </div>
    </div>
   </footer>

   <!-- Login Modal -->
 <div class="modal" id="loginModal" style="display:none;">
  <div class="modal-content">
    <span class="close" id="close-login">&times;</span>
    <h2>Login to Continue</h2>
    <form method="POST" action="handlers/login_handler.php">
      <!-- Hidden field to identify THIS login form -->
      <input type="hidden" name="source" value="main">

      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <button type="submit">Login</button>
    </form>
  </div>
 </div>

 <!-- Register Modal -->
 <div class="modal" id="registerModal" style="display:none;">
  <div class="modal-content">
    <span class="close" id="close-register">&times;</span>
    <h2>Create an Account</h2>
    <form method="POST" action="handlers/register_handler.php">
      <label>Full Name</label>
      <input type="text" name="name" required>
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Phone</label>
      <input type="text" name="phone" required>
      <label>Password</label>
      <input type="password" name="password" required>
       <select name="selectRole" id="selectRole" required> 
        <option value="Select Role">Select Role</option>
        <option value="admin">Admin</option>
        <option value="trainee">Trainee</option>
        <option value="trainer">Trainer</option>
      </select>
      <button type="submit">Register</button>
    </form>
  </div>
 </div>


  <!-- Toast Container -->
 <div id="toast-container"></div>

  
  <script src="index.js" defer></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
  const lazyImages = document.querySelectorAll("img.lazy");
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.classList.remove("lazy");
        observer.unobserve(img);
      }
    });
  });
  lazyImages.forEach(img => imageObserver.observe(img));
  });

  //-- Smooth fade-in on image load --
  document.addEventListener('DOMContentLoaded', () => {
  const imgs = document.querySelectorAll('.blog-img');
  imgs.forEach(img => {
    img.addEventListener('load', () => {
      img.classList.add('visible');
    }, { once: true });
  });
  });
  </script>
  <script>
  window.userStatus = {
  isLoggedIn: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>, 
  isRegistered: <?php echo isset($_SESSION['is_registered']) ? 'true' : 'false'; ?>
  };
  </script>


  <script>
  document.addEventListener("DOMContentLoaded", function() {
  const isLoggedIn = window.userStatus.isLoggedIn; 
  const isRegistered = window.userStatus.isRegistered;

  const bookBtn = document.getElementById('bookNow');
  bookBtn.addEventListener('click', function(event) {
    event.preventDefault(); // always prevent default

    if (!isLoggedIn) {
      if (!isRegistered) {
        document.getElementById('registerModal').style.display = 'flex';
      } else {
        document.getElementById('loginModal').style.display = 'flex';
      }
    } else {
      // logged-in users go to booking page
      window.location.href = "booking.php";
    }
  });

  // Login/Register links
  document.getElementById('login-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('loginModal').style.display = 'flex';
  });

  document.getElementById('register-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('registerModal').style.display = 'flex';
  });

  // Close buttons
  document.getElementById('close-login').addEventListener('click', function() {
    document.getElementById('loginModal').style.display = 'none';
  });

  document.getElementById('close-register').addEventListener('click', function() {
    document.getElementById('registerModal').style.display = 'none';
  });

  // Click outside modal to close
  window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('loginModal')) {
      document.getElementById('loginModal').style.display = 'none';
    }
    if (event.target === document.getElementById('registerModal')) {
      document.getElementById('registerModal').style.display = 'none';
    }
  });
  });
  </script>

  <!-- Join Event Modal Script -->
 <script>
  const openBtn = document.getElementById('openJoinModal');
  const modal = document.getElementById('joinFormModal');
  const closeBtn = document.getElementById('closeModal');
  const form = document.getElementById('joinForm');

  // Show modal
  openBtn.addEventListener('click', () => {
    modal.style.display = 'flex'; 
  });

  // Close modal
  closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  // Close modal when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none';
  });

  // Handle form submission
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = {
      fullname: document.getElementById('fullname').value.trim(),
      email: document.getElementById('email').value.trim(),
      phone: document.getElementById('phone').value.trim(),
      location: document.getElementById('location').value.trim(),
      goals: document.getElementById('goals').value.trim(),
      sessionType: document.getElementById('sessionType').value // FIXED key name
    };

    try {
      const response = await fetch('handlers/join_form_handler.php', { // adjust path if needed
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      });

      const result = await response.json();

      if (result.success) {
        alert('✅ ' + result.message);
        form.reset();
        modal.style.display = 'none';
      } else {
        alert('⚠️ ' + result.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('❌ Something went wrong. Please try again.');
    }
  });
 </script>

</body>
</html>
