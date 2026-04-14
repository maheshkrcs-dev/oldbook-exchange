</div>

<footer class="bg-dark text-white text-center p-3 mt-5">
© <?= date('Y') ?> OldBook Exchange
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function updateNotifications() {
    fetch("<?= $base_url ?>includes/get_notifications.php")
    .then(res => res.json())
    .then(data => {

        // CART
        let cart = document.getElementById("cart-count");
        if (cart) {
            cart.innerText = data.cart;
            cart.style.display = data.cart > 0 ? "inline-block" : "none";
        }

        // WISHLIST
        let wish = document.getElementById("wishlist-count");
        if (wish) {
            wish.innerText = data.wishlist;
            wish.style.display = data.wishlist > 0 ? "inline-block" : "none";
        }

        // MESSAGES
        let msg = document.getElementById("msg-count");
        if (msg) {
            msg.innerText = data.messages;
            msg.style.display = data.messages > 0 ? "inline-block" : "none";
        }

    })
    .catch(err => console.log("Notification error:", err));
}

// 🔁 RUN EVERY 5 SECONDS
setInterval(updateNotifications, 5000);

// RUN ON LOAD
updateNotifications();
</script>
</body>
</html>