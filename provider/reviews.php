<?php
require_once '../config/database.php';
require_once '../models/Review.php';
require_once '../models/Provider.php';
require_once '../utils/helpers.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireLogin();
requireRole('provider');

$db = (new Database())->getConnection();
$reviewModel = new Review($db);

// Pagination
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$providerModel = new Provider($db);
$provider_id = $providerModel->getProviderIdByUserId($_SESSION['user_id']);
$total_reviews = $reviewModel->countByProviderId($provider_id);
$total_pages = ceil($total_reviews / $limit);

$reviews = $reviewModel->getByProviderId($provider_id, $limit, $offset);

include '../includes/header.php';
?>

<div class="container-custom py-8">
    <h2 class="text-2xl font-bold mb-4">Customer Reviews</h2>

    <?php if ($reviews->rowCount() === 0): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
            No reviews available.
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php while ($review = $reviews->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="p-4 border rounded-lg bg-white shadow">
                    <div class="flex justify-between items-center mb-1">
                        <h3 class="text-lg font-semibold"><?= htmlspecialchars($review['service_title']) ?></h3>
                        <span class="text-sm text-gray-500"><?= formatDate($review['created_at']) ?></span>
                    </div>
                    <div class="flex items-center text-yellow-500 text-sm mb-2">
                        <?= str_repeat('★', intval($review['rating'])) ?>
                        <?= str_repeat('☆', 5 - intval($review['rating'])) ?>
                        <span class="ml-2 text-gray-700"><?= $review['rating'] ?>/5</span>
                    </div>
                    <p class="text-gray-700 mb-2"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                    <div class="text-sm text-gray-600">
                        — <?= htmlspecialchars($review['customer_name']) ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-6 flex justify-center space-x-2">
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <a href="?page=<?= $p ?>"
                       class="px-3 py-1 border rounded <?= $p == $page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
