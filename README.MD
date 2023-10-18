# Simple Chat API

## Description

This project provides a simple API for a basic chat application. It allows users to create groups, join existing groups, and send messages to those groups.

## Setup

- Clone the project to your local machine.
- Navigate to the project directory.
- Install required packages: `composer install`
- Start the local development server: `php -S localhost:8000 public/index.php`

## Database Setup

To set up the SQLite database, run the following command:

\`\`\`bash
# Create tables for users, groups, and messages
sqlite3 ./database/chat.db < ./database/tables.sql
\`\`\`

## Routes

### POST `/users`

- Creates a new user
- Request body: `{ "username": "Emre" }`
- Response: `{ "id": 1 }`

### POST `/groups`

- Creates a new group
- Request body: `{ "name": "ChatGroup1" }`
- Response: `{ "id": 1 }`

### GET `/groups`

- Lists all groups
- Response: Array of groups

### POST `/groups/{id}/join`

- Joins an existing group
- Request body: `{ "user_id": 1 }`
- Response: `{ "success": true }`

### POST `/groups/{id}/message`

- Sends a message to a group
- Request body: `{ "user_id": 1, "message": "Hello" }`
- Response: `{ "success": true }`
