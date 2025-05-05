<?php
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Provider.php';
require_once '../models/Service.php';
require_once '../models/Category.php';
require_once '../utils/helpers.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireLogin();
requireRole('provider');

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$providerModel = new Provider($db);
$service = new Service($db);
$category = new Category($db);

$user->readOne($_SESSION['user_id']);
$provider = $providerModel->getByUserId($_SESSION['user_id']);

$action = $_REQUEST['action'] ?? '';
$service_id = $_REQUEST['id'] ?? null;

// Handle different actions
switch ($action) {
    case 'add':
        renderAddForm($category);
        break;

    case 'create':
        handleCreateService($service, $provider);
        break;

    case 'edit':
        renderEditForm($service, $service_id, $category);
        break;

    case 'update':
        handleUpdateService($service, $service_id);
        break;

    case 'delete':
        handleDeleteService($service, $service_id);
        break;

    default:
        renderServiceList($service, $provider, $category);
        break;
}


// Functions
function handleCreateService($service, $provider)
{
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $price_type = $_POST['price_type'];

    if (empty($title) || empty($description) || empty($category_id) || empty($price)) {
        setFlashMessage('error','All fields are required.');
        redirectBack();
    }

    $service->provider_id = $provider->id;
    $service->title = $title;
    $service->description = $description;
    $service->category_id = $category_id;
    $service->price = $price;
    $service->price_type = $price_type;

    if ($service->create()) {
        setFlashMessage('success','Service created successfully!');
    } else {
        setFlashMessage('error','Failed to create service.');
    }

    redirectToServices();
}

function handleUpdateService($service, $id)
{
    $existing = $service->readOne($id);
    if (!$existing) {
        setFlashMessage('error','Service not found.');
        redirectToServices();
    }

    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $price_type = $_POST['price_type'];

    if (empty($title) || empty($description) || empty($category_id) || empty($price)) {
        setFlashMessage('error','All fields are required.');
        redirectBack();
    }

    $service->id = $id;
    $service->title = $title;
    $service->description = $description;
    $service->category_id = $category_id;
    $service->price = $price;
    $service->price_type = $price_type;

    if ($service->update()) {
        setFlashMessage('success','Service updated successfully!' );
    } else {
        setFlashMessage('error','Failed to update service.');
    }

    redirectToServices();
}

function handleDeleteService($service, $id)
{
    $service->id = $id;
    if ($service->delete()) {
        setFlashMessage('success', 'Service deleted successfully.');
    } else {
        setFlashMessage( 'error','Failed to delete service.');
    }
    redirectToServices();
}

function renderAddForm($category)
{
    $categories = $category->readAll();
    include '../includes/header.php';
    include 'partials/service_form_add.php';
    include '../includes/footer.php';
    exit;
}

function renderEditForm($service, $id, $category)
{
    $existing_service = $service->readOne($id);
    if (!$existing_service) {
        setFlashMessage('error','Service not found.');
        redirectToServices();
    }

    $categories = $category->readAll();
    include '../includes/header.php';
    include 'partials/service_form_edit.php';
    include '../includes/footer.php';
    exit;
}

function renderServiceList($service, $provider, $category)
{
    $services = $service->getByProviderId($provider->id);
    include '../includes/header.php';
    include 'partials/service_list.php';
    include '../includes/footer.php';
}

function redirectToServices()
{
    header('Location: ' . BASE_URL . '/provider/services.php');
    exit;
}

function redirectBack()
{
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
