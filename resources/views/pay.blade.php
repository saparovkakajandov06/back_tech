<html>
<head>
    <title>Buy cool new product</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

stripe public token
<input type="text" id="public-token">
<br>
api auth token
<input type="text" id="auth-token">
<br>
amount
<input type="text" id="amount">
<br>

<button id="checkout-button">Checkout</button>

<script type="text/javascript">
    var checkoutButton = document.getElementById('checkout-button');
    var stripe = null;

    checkoutButton.addEventListener('click', function () {
        // Create an instance of the Stripe object with your publishable API key
        var publicToken = document.getElementById("public-token").value
        stripe = Stripe(publicToken);

        // Create a new Checkout Session using the server-side endpoint you
        // created in step 3.
        const formData = new FormData();
        formData.append('amount', document.getElementById('amount').value)
        formData.append('cur', 'usd')
        formData.append('success_url', 'http://localhost:8888/pay?status=success')
        formData.append('cancel_url', 'http://localhost:8888/pay?status=cancel')

        fetch('/api/stripe_deposit', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + document.getElementById("auth-token").value
            },
            body: formData,
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (session) {
            return stripe.redirectToCheckout({sessionId: session.id});
        })
        .then(function (result) {
            // If `redirectToCheckout` fails due to a browser or network
            // error, you should display the localized error message to your
            // customer using `error.message`.
            if (result.error) {
                alert(result.error.message);
            }
        })
        .catch(function (error) {
            console.error('Error:', error);
        });
    });
</script>
</body>
</html>
