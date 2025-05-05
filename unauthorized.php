<?php
include 'includes/header.php'; // or appropriate path
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-md text-center">
        <h1 class="text-3xl font-bold text-red-600 mb-4">403 - Unauthorized</h1>
        <p class="text-gray-700 mb-6">You do not have permission to access this page.</p>
        <a href="<?= BASE_URL ?>/index.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Go Back Home
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
