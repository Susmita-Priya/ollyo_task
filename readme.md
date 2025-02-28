# Simple Checkout System with PayPal Integration

This is a simple checkout system built with vanilla PHP (no frameworks) that integrates with the PayPal payment gateway. It allows users to process a predefined cart of products through PayPal checkout.

---

## **Features**
- **PayPal Integration**: Seamless payment processing using PayPal's REST API.
- **Object-Oriented PHP**: Follows OOP principles and PSR coding standards.
- **PSR-4 Autoloading**: Uses Composer for dependency management and autoloading.
- **Dynamic Views**: Supports dynamic rendering of views with data.
- **Payment Verification**: Validates payment amounts and transaction IDs.
- **Error Handling**: Proper error handling for failed payments.
- **Optional Recurring Payments**: Supports subscription billing (bonus feature).

---

## **Requirements**
- PHP 8.1 or higher
- Composer (for dependency management)
- PayPal Developer Account (for sandbox/testing credentials)

---

## **Setup Instructions**

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Susmita-Priya/ollyo_task.git
   cd ollyo-task
   ```

2. **Install Dependencies**
   Install the required dependencies using Composer:
   ```bash
   composer install
   ```

3. **Configure PayPal Credentials**
   - Create a `.env` file in the root directory and add your PayPal credentials:
     ```
     PAYPAL_CLIENT_ID=your_client_id
     PAYPAL_CLIENT_SECRET=your_client_secret
     PAYPAL_MODE=sandbox
     ```
   - Alternatively, you can directly update the `config/paypal.php` file with your credentials.

4. **Run the Application**
   Start a local PHP server:
   ```bash
   php -S localhost:8000
   ```
   Access the application in your browser at `http://localhost:8000`.

---

## **Project Structure**
```
ollyo-php-developer-task/
├── src/
│   ├── Controllers/          # Payment controllers (optional)
│   ├── Views/                # View files (e.g., checkout, success, failure)
├── config/                   # Configuration files (e.g., PayPal credentials)
├── helper.php                # Helper functions (e.g., view rendering)
├── index.php                 # Entry point for the application
├── composer.json             # Composer configuration
├── .env                      # Environment variables
└── README.md                 # Project documentation
```

---

## **API Endpoints**
| Endpoint              | Description                                      |
|-----------------------|--------------------------------------------------|
| `GET /`               | Homepage (redirects to checkout)                |
| `GET /checkout`       | Checkout page with cart details                 |
| `POST /create-payment`| Creates a PayPal payment intent                 |
| `GET /payment-success`| Handles successful PayPal payment callback      |
| `GET /payment-failure`| Handles failed PayPal payment callback          |

---

## **Usage**

### **1. Checkout Page**
- Access the checkout page at `http://localhost:8000/checkout`.
- Displays a list of products, subtotal, shipping cost, and total amount.
- Users can proceed to PayPal checkout.

### **2. PayPal Payment**
- Redirects users to PayPal for payment processing.
- On successful payment, users are redirected to the `success.php` view.
- On failed payment, users are redirected to the `failure.php` view.

