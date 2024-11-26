# OnStar Token Generator

This project provides a simple web interface for generating an OnStar authentication token using user credentials. It securely communicates with the OnStar API to authenticate the user and verify their credentials. The final output is a unique URL containing the generated token, which can be used for further integrations.

## Features

1. **Authentication**:
   - Users can log in by providing their email (username) and password.
   - The system sends the credentials to the OnStar authentication API using the `node-oauth2-gm` library for PKCE flow.

2. **Two-Factor Verification**:
   - After successful authentication, users are prompted to enter a verification code received via email or SMS.
   - The verification code is validated via the OnStar API.

3. **Token Generation**:
   - On successful verification, a secure token (hash) is generated.
   - The token is stored in a file named after the hash, and the corresponding email is saved within.

4. **Token Retrieval**:
   - The generated token can be accessed through a unique URL (`https://token.mondemarreur.com/token/<hash>`).
   - A separate script (`token.php`) allows retrieval of the email address associated with the token.

5. **User Privacy**:
   - The system does not store passwords or other sensitive information permanently.
   - Data is securely handled during the authentication and verification processes.

## Requirements

- **PHP 7.4+**
- **cURL enabled** in your PHP installation
- **Bootstrap** (via CDN for styling)
- [joelvandal/node-oauth2-gm](https://github.com/joelvandal/node-oauth2-gm) for OAuth2 authentication.

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/joelvandal/onstar-token-gen.git
   cd onstar-token-gen
