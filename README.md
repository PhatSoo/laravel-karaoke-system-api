# Customer Management API

Welcome to the Customer Management API! This powerful tool allows you to handle all aspects of customer data management seamlessly. Below are the available endpoints you can use:

## Endpoints

### `GET /customers`

Retrieve a list of all customers.

### `POST /customers`

Create a new customer entry.

-   **Body Parameters:**
    -   `name` (string): The name of the customer.
    -   `email` (string): The email address of the customer.
    -   `phone` (string): The phone number of the customer.

### `GET /customers/{id}`

Get details for a specific customer by their unique ID.

-   **Path Parameters:**
    -   `id` (integer): The unique ID of the customer.

### `PUT /customers/{id}`

Update information for an existing customer.

-   **Path Parameters:**
    -   `id` (integer): The unique ID of the customer.
-   **Body Parameters:**
    -   `name` (string, optional): The name of the customer.
    -   `email` (string, optional): The email address of the customer.
    -   `phone` (string, optional): The phone number of the customer.

### `DELETE /customers/{id}`

Remove a customer from the database.

-   **Path Parameters:**
    -   `id` (integer): The unique ID of the customer.

Each endpoint ensures you have full control over your customers' data with easy-to-use HTTP methods. Start integrating now and elevate your application's user management capabilities!
