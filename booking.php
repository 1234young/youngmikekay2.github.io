<?php

session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

function clean(string $s): string {
  return trim($s);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name  = clean($_POST['name'] ?? '');
  $email = clean($_POST['email'] ?? '');
  $phone = clean($_POST['phone'] ?? '');
  $date  = clean($_POST['date'] ?? '');
  $plan  = clean($_POST['plan'] ?? '');
  $notes = clean($_POST['notes'] ?? '');

  // Validation
  if ($name === '' || strlen($name) > 120) $errors[] = 'Name is required (max 120 chars).';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
  if ($phone === '' || strlen($phone) > 40) $errors[] = 'Phone is required (max 40 chars).';
  if ($date !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d) $errors[] = 'Date must be YYYY-MM-DD.';
  }

  if (empty($errors)) {
    $csvPath = __DIR__ . '/bookings.csv';
    $needHeader = !file_exists($csvPath) || filesize($csvPath) === 0;

    $fh = @fopen($csvPath, 'a');
    if ($fh === false) {
      $errors[] = 'Unable to open bookings file for writing. Check permissions.';
    } else {
      if (flock($fh, LOCK_EX)) {
        if ($needHeader) {
          fputcsv($fh, ['timestamp','name','email','phone','date','plan','notes']);
        }
        fputcsv($fh, [date('c'), $name, $email, $phone, $date, $plan, $notes]);
        fflush($fh);
        flock($fh, LOCK_UN);
      } else {
        $errors[] = 'Could not lock bookings file.';
      }
      fclose($fh);
      if (empty($errors)) {
        // Post/Redirect/Get to avoid double submits
        header('Location: booking.php?success=1');
        exit;
      }
    }
  }
}

// small helper to escape output
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$success = isset($_GET['success']) && (int)$_GET['success'] === 1;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Book a Session — Workout Planner</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* === BOOKING FORM STYLING === */
      /* === BOOKING FORM (Same Style as Register Form) === */
.form-wrap {
  max-width: 700px;
  margin: 3rem auto;
  padding: 2rem 2.5rem;
  background: var(--dark, #0f1724);
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
  color: #fff;
  font-family: "Poppins", sans-serif;
  transition: all 0.3s ease;
}

.form-wrap:hover {
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
}

.form-wrap h2 {
  text-align: center;
  color: var(--accent-2, #ffd166);
  font-weight: 600;
  margin-bottom: 1.5rem;
  letter-spacing: 0.5px;
}

.form-row {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-row .col {
  flex: 1;
  min-width: 48%;
}

label {
  display: block;
  font-size: 0.9rem;
  color: #eee;
  margin-bottom: 0.4rem;
  letter-spacing: 0.3px;
}

input,
textarea,
select {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  font-size: 0.95rem;
  transition: border-color 0.3s, box-shadow 0.3s, background 0.3s;
}

input:focus,
textarea:focus,
select:focus {
  border-color: var(--accent-2, #ffd166);
  background: rgba(255, 255, 255, 0.12);
  box-shadow: 0 0 0 3px rgba(255, 209, 102, 0.25);
  outline: none;
}

textarea {
  resize: vertical;
  min-height: 110px;
}

/* === BUTTON === */
button {
  display: block;
  width: 100%;
  background: var(--accent, #ff6b6b);
  color: #fff;
  font-size: 1rem;
  font-weight: 600;
  padding: 0.9rem;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.3s, transform 0.2s;
  margin-top: 1rem;
}

button:hover {
  background: #ff8585;
  transform: translateY(-2px);
}

/* === ALERTS (optional feedback) === */
.errors {
  background: rgba(255, 107, 107, 0.15);
  border-left: 4px solid var(--accent, #ff6b6b);
  color: #ffb3b3;
  padding: 0.8rem 1rem;
  border-radius: 6px;
  margin-bottom: 1rem;
}

.success {
  background: rgba(255, 209, 102, 0.15);
  border-left: 4px solid var(--accent-2, #ffd166);
  color: #ffe9a6;
  padding: 0.8rem 1rem;
  border-radius: 6px;
  margin-bottom: 1rem;
}


  </style>
</head>
<body>
  <main class="form-wrap">
    <h1>Book a Session</h1>

    <?php if ($success): ?>
      <div class="success">Thanks — your booking was saved.</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="errors">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="handlers/booking_handler.php" novalidate>
      <div class="form-row">
        <div class="col">
          <label for="name">Full name</label>
          <input id="name" name="name" type="text" required maxlength="120" value="<?= e($_POST['name'] ?? '') ?>">
        </div>
        <div class="col">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="col">
          <label for="phone">Phone</label>
          <input id="phone" name="phone" type="tel" required maxlength="40" value="<?= e($_POST['phone'] ?? '') ?>">
        </div>
        <div class="col">
          <label for="date">Preferred date</label>
          <input id="date" name="date" type="date" value="<?= e($_POST['date'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="col">
          <label for="plan">Plan</label>
          <select id="plan" name="plan">
            <option value="">Select a plan</option>
            <option value="Basic" <?= (($_POST['plan'] ?? '') === 'Basic') ? 'selected' : '' ?>>Basic</option>
            <option value="Premium" <?= (($_POST['plan'] ?? '') === 'Premium') ? 'selected' : '' ?>>Premium</option>
            <option value="Family" <?= (($_POST['plan'] ?? '') === 'Family') ? 'selected' : '' ?>>Family</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom:.75rem;">
        <label for="notes">Notes / message</label>
        <textarea id="notes" name="notes" rows="4"><?= e($_POST['notes'] ?? '') ?></textarea>
      </div>

      <div style="display:flex;gap:.5rem;align-items:center">
        <button type="submit">Submit Booking</button>
        <a href="index.html" style="color:#666;margin-left:1rem">Cancel</a>
      </div>
    </form>
  </main>
</body>
</html>
