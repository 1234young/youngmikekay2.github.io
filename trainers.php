<?php
// trainers.php

$trainers = [
    [
        'name' => 'Jane Doe',
        'email' => 'janedoe@gmail.com',
        'phone' => '+254 700123456',
        'image' => 'images/jane.webp'
    ],
    [
        'name' => 'John Smith',
        'email' => 'johnsmith@gmail.com',
        'phone' => '+254 701234567',
        'image' => 'images/john.webp'
    ],
    [
        'name' => 'Mary Johnson',
        'email' => 'maryjohnson@gmail.com',
        'phone' => '+254 702345678',
        'image' => 'images/mary.webp'
    ],
    [
        'name' => 'James Lee',
        'email' => 'jameslee@gmail.com',
        'phone' => '+254 703456789',
        'image' => 'images/james.webp'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>trainers.dev/work-out-planner</title>
    <style>
      /* Trainers Page Styles */
      header {text-align: center;margin: 40px 0 20px;}
      header h1 {font-size: 3.5rem;margin-bottom: 10px; color: #333;}
      header p {font-size: 1.5rem;color: #333;}

      /* Container for trainer cards */
      .trainers.container {display: grid;grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));gap: 
        20px;padding: 20px;max-width: 1200px;margin: 0 auto;}

     /* Individual trainer card */
     .trainer-card { background-color: #fff;border-radius: 12px;box-shadow: 0 4px 12px rgba(0,0,0,0.1);text-align:
        transition: transform 0.3s, box-shadow 0.3s; center;padding: 20px;}
     .trainer-card:hover {transform: translateY(-5px);box-shadow: 0 8px 20px rgba(0,0,0,0.15);}
     .trainer-card img {width: 150px;height: 150px;border-radius: 50%;object-fit: cover;margin-bottom: 15px;
        border: 3px solid #007bff; display: block; margin-left: auto; margin-right: auto;}
     .trainer-card h3 {margin: 10px 0 5px;font-size: 1.4rem;background: linear-gradient(90deg, #ffd166, #ff6b6b); 
        background-clip: text; color: transparent;  margin-top: 0; text-align: center;} 
     .trainer-card p {margin: 5px 0;color: #333;font-size: 1rem; text-align: center;}

     /* Responsive adjustments */
   @media (max-width: 600px) {
    header h1 {font-size: 2rem;}
    .trainer-card img {width: 100px; height: 100px;}
    }
    /* Smooth fade transition effect */
   body {transition: opacity 0.5s ease; opacity: 1; background: linear-gradient(90deg, #ffd166, #ff6b6b); }
   body.fade-out {opacity: 0;}

    </style>
</head>
<body>

<header>
    <h1>Meet Our Trainers</h1>
    <p>Learn from the best trainers in the industry!</p>
</header>

<section class="trainers container">
    <?php foreach($trainers as $trainer): ?>
        <div class="trainer-card">
            <img src="<?php echo $trainer['image']; ?>" alt="<?php echo $trainer['name']; ?>">
            <h3><?php echo $trainer['name']; ?></h3>
            <p>Email: <?php echo $trainer['email']; ?></p>
            <p>Phone: <?php echo $trainer['phone']; ?></p>
        </div>
    <?php endforeach; ?>
 </section>
 <script>
 document.addEventListener("DOMContentLoaded", function() {
  document.getElementById('slide-btn').addEventListener('click', function() {
    window.location.href = "trainers.php";
  });
 });
 </script>

  <script>
  window.addEventListener('load', () => {
    document.body.classList.add('loaded');
  });
  </script>


</body>
</html>
