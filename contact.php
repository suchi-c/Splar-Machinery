<?php
include 'includes/db.php';

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $place = trim($_POST['place']);
    $subject = "Contact Form Enquiry"; // Default subject
    $message = trim($_POST['message']);

    // Append place to message
    if (!empty($place)) {
        $message .= "\n\nLocation: " . $place;
    }

    // Basic validation
    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO enquiries (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            
            // Redirect to avoid resubmission on refresh
            header("Location: contact.php?msg=sent");
            exit;
        } catch (PDOException $e) {
            $msg = "Error sending message. Please try again later.";
        }
    } else {
        $msg = "Please fill in all required fields.";
    }
}

// Check for success message in URL
if (isset($_GET['msg']) && $_GET['msg'] == 'sent') {
    $msg = "Your message has been sent successfully. We will contact you shortly.";
}

// Pre-fill subject if passed via URL (e.g. from product details)
$pre_subject = isset($_GET['subject']) ? htmlspecialchars($_GET['subject']) : '';
?>

<?php include 'includes/header.php'; ?>

    <!-- Contact Section -->
    <section class="section">
        <div class="container">
            <h1 class="contact-title">Contact us</h1>
            
            <div class="contact-split-layout">
                <!-- Left Column: Contact Info -->
                <div class="contact-info-new">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>SPLAR MACHINERY PVT. LTD.,</h3>
                            <p>Vazhakala Complex, Bayalu SiddaAshrama Compound, Behind 460 A, Peenya 4th Phase Peenya Industrial Area Bangalore 560 058, Karnataka, India</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <div>
                            <h3>Phone</h3>
                            <p>+91 99018 63914</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Mail</h3>
                            <p>splar@splar-machinery.com</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form -->
                <div class="contact-form-new">
                    <?php if (!empty($msg)): ?>
                        <div class="alert" style="background: <?php echo strpos($msg, 'successfully') !== false ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo strpos($msg, 'successfully') !== false ? '#155724' : '#721c24'; ?>;">
                            <?php echo $msg; ?>
                        </div>
                    <?php endif; ?>

                    <form action="contact.php" method="POST">
                        <div class="form-row">
                            <input type="text" name="name" class="input-new" placeholder="Name" required>
                            <input type="text" name="phone" class="input-new" placeholder="Phone">
                        </div>
                        <div class="form-row">
                            <input type="email" name="email" class="input-new" placeholder="Email" required>
                            <input type="text" name="place" class="input-new" placeholder="Place">
                        </div>
                        <div class="form-group">
                            <textarea name="message" rows="5" class="input-new" placeholder="Message" required></textarea>
                        </div>
                        <button type="submit" class="btn-submit-new">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Full Width Map -->
    <section class="map-section">
        <iframe src="https://maps.google.com/maps?q=Vazhakala+Complex,+Peenya+4th+Phase,+Bangalore&t=&z=15&ie=UTF8&iwloc=&output=embed" 
            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </section>
</main>

<?php include 'includes/footer.php'; ?>