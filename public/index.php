<?php

require_once '../app/config/config.php';
require_once '../app/core/Database.php';
require_once '../app/core/Router.php';

// Initialize Router
$router = new Router();

// Define Routes
$router->add('GET', '/', 'HomeController', 'index');
$router->add('GET', '/login', 'AuthController', 'login');
$router->add('POST', '/login', 'AuthController', 'authenticate');
$router->add('GET', '/register', 'AuthController', 'register');
$router->add('GET', '/logout', 'AuthController', 'logout');
$router->add('POST', '/register', 'AuthController', 'store');
$router->add('POST', '/api/ai/analyze', 'AIController', 'analyze'); // AI Endpoint

// Dashboard Routes
$router->add('GET', '/dashboard', 'DashboardController', 'index');

// Task Routes
$router->add('GET', '/tasks/create', 'TaskController', 'create');
$router->add('POST', '/tasks/store', 'TaskController', 'store');

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
