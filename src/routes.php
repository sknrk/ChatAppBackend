<?php
// src/routes.php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

function setup_routes(App $app)
{
    $pdo = new PDO('sqlite:' . __DIR__ . '/../database/chat.db');

    // POST endpoint to create a new user
    $app->post('/users', function (Request $request, Response $response, $args) use ($pdo) {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';

        // Insert new user into database
        $stmt = $pdo->prepare('INSERT INTO users (username) VALUES (:username)');
        $stmt->execute([':username' => $username]);

        // Return new user ID as JSON response
        $response->getBody()->write(json_encode(['id' => $pdo->lastInsertId()]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // POST endpoint to create a new group
    $app->post('/groups', function (Request $request, Response $response, $args) use ($pdo) {
        $data = $request->getParsedBody();
        $name = $data['name'] ?? '';

        // Insert new group into database
        $stmt = $pdo->prepare('INSERT INTO groups (name) VALUES (:name)');
        $stmt->execute([':name' => $name]);

        // Return new group ID as JSON response
        $response->getBody()->write(json_encode(['id' => $pdo->lastInsertId()]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // GET endpoint to fetch all groups
    $app->get('/groups', function (Request $request, Response $response, $args) use ($pdo) {
        // Fetch all groups from database
        $stmt = $pdo->query('SELECT * FROM groups');
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return groups as JSON response
        $response->getBody()->write(json_encode($groups));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // POST endpoint to join a group
    $app->post('/groups/{id}/join', function (Request $request, Response $response, $args) use ($pdo) {
        $groupId = $args['id'];
        $data = $request->getParsedBody();
        $userId = $data['user_id'] ?? '';
                
        // Add user to group in database
        $stmt = $pdo->prepare('INSERT INTO group_members (group_id, user_id) VALUES (:group_id, :user_id)');
        $stmt->execute([':group_id' => $groupId, ':user_id' => $userId]);
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // POST endpoint to send a message in a group
    $app->post('/groups/{id}/message', function (Request $request, Response $response, $args) use ($pdo) {
        
        $groupId = $args['id'];
        $data = $request->getParsedBody();
        $userId = $data['user_id'] ?? '';
    
        // Check if user is part of the group
        $stmt = $pdo->prepare('SELECT * FROM group_members WHERE group_id = :group_id AND user_id = :user_id');
        $stmt->execute([':group_id' => $groupId, ':user_id' => $userId]);
        $isMember = $stmt->fetch();
    
        if (!$isMember) {
            // User not part of group, unauthorized to send messages
            $response->getBody()->write(json_encode(['error' => 'User is not a member of the group']));
            return $response->withStatus(403)
                            ->withHeader('Content-Type', 'application/json');
        }
    
        // User is a group member, send message
        $message = $data['message'] ?? '';
        $stmt = $pdo->prepare('INSERT INTO messages (group_id, user_id, message) VALUES (:group_id, :user_id, :message)');
        $stmt->execute([':group_id' => $groupId, ':user_id' => $userId, ':message' => $message]);
    
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // GET endpoint to fetch all messages in a group
    $app->get('/groups/{id}/messages', function (Request $request, Response $response, $args) use ($pdo) {
        $groupId = $args['id'];
        
        // Fetch all messages for the group from the database
        $stmt = $pdo->prepare('SELECT * FROM messages WHERE group_id = :group_id');
        $stmt->execute([':group_id' => $groupId]);

        // Return messages as JSON response
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($messages));
        return $response->withHeader('Content-Type', 'application/json');
    });
}
