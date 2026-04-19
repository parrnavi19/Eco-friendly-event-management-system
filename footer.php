    </main>

    <!-- Feedback Section -->
    <section class="feedback-section">
        <div class="feedback-container">
            <h3>💬 Share Your Feedback</h3>
            <p>Help us improve EcoEvents and make our community greener!</p>

            <?php if (isset($_GET['feedback']) && $_GET['feedback'] === 'success'): ?>
                <div class="alert alert-success" style="max-width: 500px; margin: 0 auto 1rem;">
                    ✅ Thank you for your feedback!
                </div>
            <?php endif; ?>

            <form action="feedback.php" method="POST" class="feedback-form">
                <div class="feedback-row">
                    <input type="text" name="name" class="feedback-input" placeholder="Your Name" required>
                    <input type="email" name="email" class="feedback-input" placeholder="Your Email" required>
                </div>
                <select name="type" class="feedback-input" required>
                    <option value="" disabled selected>Feedback Type</option>
                    <option value="suggestion">Suggestion</option>
                    <option value="bug">Report a Bug</option>
                    <option value="compliment">Compliment</option>
                    <option value="other">Other</option>
                </select>
                <textarea name="message" class="feedback-input" rows="3" placeholder="Write your feedback here..." required></textarea>
                <button type="submit" class="btn btn-primary feedback-btn">Send Feedback 🌿</button>
            </form>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> EcoEvents. Empowering Sustainable Communities (SDG 11 & 12).</p>
    </footer>
    <script>
        // Form validations and dynamic elements could go here
    </script>
</body>
</html>
