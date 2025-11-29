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

  // Expose whether the user is logged in (adjust to your session key for user identity)
  $isLogged = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>communityworkoutevents.dev/Workout Planner</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;margin: 0; padding: 0;background: linear-gradient(180deg,#f7fafc,#eef2f7);
      color: var(--dark); line-height: 1.45;}

    header {padding: 5px; text-align: center; position: sticky; top: 0; z-index: 50;background: rgba(255,255,255,0.95);
      backdrop-filter: blur(6px);box-shadow: 0 6px 18px rgba(15,23,36,0.06);}

    .planner {
      background: linear-gradient(90deg, #ffd166, #ff6b6b);
      background-clip: text; color: transparent;font-size: 2.0rem; font-weight: 600;}

    .events-hero {
      background: url('images/community-workout.webp') center/cover no-repeat;
      color: white; text-align: center; padding: 140px 0px;
      min-height: 300px; display: flex; flex-direction: column; justify-content: center;
      box-shadow: inset 0 0 0 1000px rgba(110, 110, 110, 0.4);
    }

    .events-hero h1 {font-size: 2.5rem;background: linear-gradient(90deg, #ffd166, #ff6b6b);
      background-clip: text; color: transparent;margin-bottom: 15px;}

    .events-hero p {max-width: 700px;margin: 0 auto; font-size: 1.1rem;line-height: 1.6;
      text-shadow: 0 2px 5px rgba(0,0,0,0.4);}

    .event-section {max-width: 1100px;margin: 20px auto;padding: 0 20px;text-align: center;}
    .event-section h2 {
      background: linear-gradient(90deg, #ffd166, #ff6b6b);background-clip: text; color: transparent;
      font-size: 2rem;margin-bottom: 30px; margin-top: 30px;}

    .event-list {display: grid;grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));gap: 25px;margin-top: 20px;}

    .event-card {background: white; border-radius: 15px; box-shadow: 0 5px 10px rgba(0,0,0,0.1); padding: 25px;text-align: center;transition: transform 0.3s ease;}

    .event-card:hover {transform: translateY(-5px);}

    .event-card h3 {margin-top: 0;color: #2c3e50;}

    .event-card p {font-size: 0.95rem;color: #333; margin-bottom: 15px;}

    .btn {background: linear-gradient(90deg, #ffd166, #ff6b6b);color: white; padding: 10px 20px;
      border-radius: 8px;border: none;cursor: pointer;font-weight: 500;transition: background 0.3s ease; }

    .btn:hover {background: #219150;}

    .event-img {width: 100%;height: 180px;object-fit: cover;border-radius: 12px 12px 0 0;}

    footer {text-align: center;padding: 30px 15px;background: #111;margin-top: 50px; font-size: 1.2rem;}
    footer p{ background: linear-gradient(90deg, #ffd166, #ff6b6b); background-clip: text; color: transparent;}

    /* ===== Modal Styles ===== */
    .modal {display: none;position: fixed; inset: 0;background: rgba(0,0,0,0.6);justify-content: center;
      align-items: center;z-index: 999;}

    .modal-content {background: #fff;padding: 30px;border-radius: 15px;text-align: center;width: 90%;
      max-width: 400px;box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: pop 0.3s ease;}

    @keyframes pop {
      from { transform: scale(0.8); opacity: 0; }to { transform: scale(1); opacity: 1; }
    }

    .modal-content h3 {margin-top: 0;}
    .modal-content button {margin: 10px;}
    #loginForm input {color: #ffd166;background: #fff;border: 1px solid #ffd166; padding: 10px; margin: 10px 0; width: 80%; border-radius: 8px; }
    .success {color: #27ae60;font-weight: bold; }
    .error {color: red;font-weight: bold;}
    .muted {opacity: 0.9;}
    .link-like { background: none; border: none; color: #ff6b6b; text-decoration: none; cursor:pointer; padding:0; font-size: 0.95rem; }
    @media (max-width:600px){
      .events-hero { padding: 80px 15px; }
    }
  </style>
</head>
<body>

  <header class="planner">Workout Planner</header>

  <section class="events-hero">
    <h1>Community Workout Events</h1>
    <p>
      Join community workout events that keep you accountable and inspired.  
      Exchange training tips, track progress together, and grow stronger  
      with people who share your goals.
    </p>
  </section>

  <section class="event-section">
    <h2>Upcoming Events</h2>

    <div class="event-list">
      <?php
        $events = [
          ["name"=>"Morning Run Club","desc"=>"Meet every Saturday at 6 AM for a refreshing 5K run through the city park.","img"=>"images/run.webp"],
          ["name"=>"Outdoor Bootcamp","desc"=>"Challenge yourself with high-intensity bodyweight and circuit training sessions every Wednesday.","img"=>"images/hiit-group.webp"],
          ["name"=>"Yoga & Flexibility Sundays","desc"=>"Calm your mind and strengthen your core every Sunday evening at the Wellness Studio.","img"=>"images/tai-chi.webp"],
          ["name"=>"Group Cycling Challenge","desc"=>"Join our weekend cycling team as we ride scenic routes while burning calories and bonding.","img"=>"images/cyclists.webp"],
          ["name"=>"HIIT in the Park","desc"=>"Experience high-energy interval training sessions every Tuesday and Thursday morning.","img"=>"images/group-hiit.webp"],
          ["name"=>"Weekend Hike & Fitness","desc"=>"Explore local trails while engaging in bodyweight exercises every Saturday morning.","img"=>"images/ladies-hiit.webp"],
        ];
      ?>

      <?php foreach ($events as $event): ?>
        <div class="event-card">
          <img src="<?php echo $event['img']; ?>" alt="<?php echo $event['name']; ?>" class="event-img">
          <div class="event-content">
            <h3><?php echo $event['name']; ?></h3>
            <p><?php echo $event['desc']; ?></p>
            <button class="btn join-btn" data-event="<?php echo htmlspecialchars($event['name']); ?>">Join Now</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!--  Join Modal  -->
  <div class="modal" id="joinModal">
    <div class="modal-content">
      <h3 id="modalTitle">Join Event</h3>
      <p id="modalText">Are you sure you want to join this event?</p>
      <div id="modalButtons">
        <button class="btn" id="confirmJoin">Yes, Join</button>
        <button class="btn" style="background:#ccc; color:#000;" id="cancelJoin">Cancel</button>
      </div>
      <p id="modalMessage"></p>
    </div>
  </div>

  <!--  Login Modal  -->
  <div class="modal" id="loginModal">
    <div class="modal-content">
      <span class="close-btn" id="closeLoginModal" style="position:absolute; right:18px; top:12px; cursor:pointer; font-size:20px;">&times;</span>
      <h3>Login to Continue</h3>
      <form id="loginForm" method="POST" >
        <input type="hidden" name="source" value="events">

        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Login</button>
      </form>
      <p id="loginMessage" class="error"></p>
      <p style="margin-top:8px;">
        <button class="link-like" onclick="window.location.href='index.php#registerModal'">Don't have an account? Register here</button>
      </p>
    </div>
  </div>

  <!--  Login Success Popup -->
  <div class="modal" id="loginSuccessModal">
    <div class="modal-content">
      <h3 class="success">Login was Successful</h3>
    </div>
  </div>

    <!-- Expose login state to JavaScript -->
    <script>
      const IS_LOGGED_IN = <?php echo json_encode($isLogged); ?>;
    </script>

  <footer>
    <p>© <?php echo date("Y"); ?> Workout Planner. All Rights Reserved.</p>
  </footer>

  <script>
const modal = document.getElementById('joinModal');
const modalTitle = document.getElementById('modalTitle');
const modalText = document.getElementById('modalText');
const modalMessage = document.getElementById('modalMessage');
const confirmJoin = document.getElementById('confirmJoin');
const cancelJoin = document.getElementById('cancelJoin');
const loginModal = document.getElementById('loginModal');
const loginMessage = document.getElementById('loginMessage');
const loginSuccessModal = document.getElementById('loginSuccessModal');
const modalButtons = document.getElementById('modalButtons');
let selectedEvent = "";

// Null check helper function
function getElement(id) {
    const element = document.getElementById(id);
    if (!element) {
        console.error(`Element with id '${id}' not found`);
    }
    return element;
}

// Open Join Modal (check login state first)
document.querySelectorAll('.join-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    selectedEvent = btn.dataset.event;

    // If not logged in, prompt login immediately
    if (typeof IS_LOGGED_IN === 'undefined' || !IS_LOGGED_IN) {
      if (loginMessage) {
        loginMessage.textContent = 'Please login to continue';
        loginMessage.className = 'muted';
      }
      // close any open join modal and open login modal
      if (modal) modal.style.display = 'none';
      if (loginModal) loginModal.style.display = 'flex';
      return;
    }

    // User is logged in, proceed to show join confirmation
    modalTitle.textContent = selectedEvent;
    modalText.textContent = `Are you sure you want to join "${selectedEvent}"?`;
    modalMessage.textContent = '';
    modalButtons.style.display = 'block';
    modal.style.display = 'flex';
  });
});

// Cancel Join
if (cancelJoin) {
    cancelJoin.addEventListener('click', () => {
        modal.style.display = 'none';
    });
}

// Submit Join (AJAX)
if (confirmJoin) {
    confirmJoin.addEventListener('click', () => {
        fetch('join_events.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `event_name=${encodeURIComponent(selectedEvent)}`
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            const contentType = res.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response');
            }
            return res.json();
        })
        .then(data => {
            modalMessage.textContent = data.message;
            modalMessage.className = data.success ? 'success' : 'error';
            modalButtons.style.display = 'none';
            
            if (!data.success && data.message.toLowerCase().includes('log in')) {
                setTimeout(() => {
                    modal.style.display = 'none';
                    loginModal.style.display = 'flex';
                }, 800);
                return;
            }
            
            if (data.success) {
                setTimeout(() => {
                    modal.style.display = 'none';
                    location.reload();
                }, 1500);
            }
        })
        .catch(error => {
            console.error('Join error:', error);
            modalMessage.textContent = "Something went wrong. Try again.";
            modalMessage.className = 'error';
        });
    });
}

// AJAX LOGIN with proper error handling
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append("source", "events");

        // Clear previous messages
        loginMessage.textContent = '';
        loginMessage.className = '';

        fetch('handlers/login_handler.php', {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            },
            body: formData
        })
        .then(res => {
            // First check response status and content type
            if (!res.ok) {
                throw new Error(`Server error: ${res.status} ${res.statusText}`);
            }
            
            // Check if response is JSON
            const contentType = res.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return res.text().then(text => {
                    throw new Error(`Server returned: ${text.substring(0, 100)}`);
                });
            }
            return res.json();
        })
        .then(data => {
            // Login failed - show specific error message
            if (!data.success) {
                loginMessage.textContent = data.message || 'Login failed. Please check your credentials.';
                loginMessage.className = 'error';
                
                // If user doesn't exist, show registration hint
                if (data.message && data.message.toLowerCase().includes('not exist') || 
                    data.message && data.message.toLowerCase().includes('no account') ||
                    data.message && data.message.toLowerCase().includes('not found')) {
                    loginMessage.textContent += ' Don\'t have an account? Register below.';
                }
                return;
            }

            // Login success
            loginMessage.textContent = data.message || 'Login successful!';
            loginMessage.className = 'success';
            
            // Close login modal and show success
            setTimeout(() => {
                loginModal.style.display = 'none';
                loginSuccessModal.style.display = 'flex';
                
                // Auto retry join after success
                setTimeout(() => {
                    loginSuccessModal.style.display = 'none';
                    fetchAfterLoginJoin();
                }, 900);
            }, 500);
        })
        .catch(error => {
            console.error('Login fetch error:', error);
            
            // Show user-friendly error messages
            if (error.message.includes('Failed to fetch')) {
                loginMessage.textContent = 'Network error. Please check your connection.';
            } else if (error.message.includes('Server returned:')) {
                loginMessage.textContent = 'Server error. Please try again.';
            } else {
                loginMessage.textContent = 'Login service unavailable. Please try again later.';
            }
            loginMessage.className = 'error';
        });
    });
}

// Retry join automatically after login
function fetchAfterLoginJoin() {
    if (!selectedEvent) {
        alert('No event selected. Please try joining again.');
        return;
    }

    fetch('join_events.php', {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `event_name=${encodeURIComponent(selectedEvent)}`
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            alert(`Successfully joined ${selectedEvent}!`);
            location.reload();
        } else {
            alert(`Failed to join: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Retry join error:', error);
        alert('Failed to join event after login. Please try again.');
    });
}

// Close Login Modal
const closeLoginModal = document.getElementById('closeLoginModal');
if (closeLoginModal) {
    closeLoginModal.addEventListener('click', () => {
        loginModal.style.display = 'none';
    });
}

// Close modals when clicking outside
window.addEventListener('click', e => {
    if (e.target === modal) modal.style.display = 'none';
    if (e.target === loginModal) loginModal.style.display = 'none';
    if (e.target === loginSuccessModal) loginSuccessModal.style.display = 'none';
});
</script>

</body>
</html>
