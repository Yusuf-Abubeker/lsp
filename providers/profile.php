<?php
require_once '../config/database.php';
require_once '../models/Provider.php';
require_once '../models/Service.php';
require_once '../models/Review.php';
require_once '../utils/helpers.php';

$provider_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($provider_id <= 0) {
    http_response_code(400);
    die('Invalid provider ID');
}

$database = new Database();
$db = $database->getConnection();

$providerModel = new Provider($db);
$provider = $providerModel->readOne($provider_id);

if (!$provider) {
    http_response_code(404);
    die('Provider not found');
}

$reviewModel = new Review($db);
$reviews = $reviewModel->getByProviderId($provider_id, 10);
$averageRating = $reviewModel->getAverageRatingByProviderId($provider_id);

include '../includes/header.php';
?>

<div class="bg-gray-50 py-10">
    <div class="container-custom max-w-6xl mx-auto px-4">
        <!-- Back Button -->
        <div class="mb-6">
            <button onclick="history.back()" class="text-sm px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                ← Back
            </button>
        </div>

        <!-- Profile Info -->
        <div class="grid md:grid-cols-3 gap-8">
            <aside class="md:col-span-1 bg-white rounded-2xl shadow-lg p-6 text-center">
                <img src="<?= htmlspecialchars($provider->profile_image ?: BASE_URL . '/assets/images/avatar-placeholder.png') ?>"
                     alt="Provider avatar" class="w-32 h-32 mx-auto rounded-full border-4 border-indigo-300 mb-4">
                
                <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($provider->name) ?></h1>
                <p class="text-gray-500 mt-1"><?= htmlspecialchars($provider->email) ?></p>
                <?php if (!empty($provider->phone)): ?>
                    <p class="text-gray-500"><?= htmlspecialchars($provider->phone) ?></p>
                <?php endif; ?>

                <?php if (!empty($provider->bio)): ?>
                    <p class="text-sm text-gray-700 mt-4 whitespace-pre-line"><?= htmlspecialchars($provider->bio) ?></p>
                <?php endif; ?>

                <div class="mt-4 text-yellow-500 text-lg font-medium">
                    <?= generateStarRating($averageRating) ?>
                    <span class="text-gray-700">(<?= number_format($averageRating, 1) ?>/5.0)</span>
                    <span class="text-xs text-gray-500 block"><?= $provider->total_reviews ?> Reviews</span>
                </div>

                <?php if ($provider->is_verified): ?>
                    <div class="mt-3 inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Verified</div>
                <?php endif; ?>
            </aside>

            <!-- Details & Reviews -->
            <main class="md:col-span-2 space-y-10">
                <!-- Location -->
                <section>
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Location</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        <?= htmlspecialchars("{$provider->address}, {$provider->city}, {$provider->state} {$provider->zip_code}") ?>
                    </p>
                    <!-- Optional Map Preview -->
                    <?php if (!empty($provider->latitude) && !empty($provider->longitude)): ?>
                        <div class="mt-4">
                            <iframe
                                src="https://www.google.com/maps?q=<?= $provider->latitude ?>,<?= $provider->longitude ?>&output=embed"
                                class="w-full rounded-xl h-56 border"
                                allowfullscreen loading="lazy"></iframe>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Services Preview -->
                <section>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Services Offered</h2>
                        <a href="<?= BASE_URL ?>/providers/services.php?id=<?= $provider_id ?>"
                           class="text-sm text-indigo-600 hover:underline">View All</a>
                    </div>

                    <?php
                    $serviceModel = new Service($db);
                    $servicesPreview = $serviceModel->getByProviderId($provider_id, 4); // Limit preview to 4
                    ?>

                    <?php if ($servicesPreview->rowCount() > 0): ?>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <?php while ($service = $servicesPreview->fetch(PDO::FETCH_ASSOC)): ?>
                                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow transition">
                                    <h3 class="text-md font-bold text-gray-900"><?= htmlspecialchars($service['title']) ?></h3>
                                    <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($service['description']) ?></p>
                                    <p class="text-indigo-600 font-semibold mt-2">$<?= number_format($service['price'], 2) ?></p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 italic">No services listed yet.</p>
                    <?php endif; ?>
                </section>

                <!-- Reviews -->
                <section>
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Recent Reviews</h2>
                    <?php if ($reviews->rowCount() > 0): ?>
                        <div class="space-y-4">
                            <?php while ($review = $reviews->fetch(PDO::FETCH_ASSOC)): ?>
                                <div class="bg-white p-4 border border-gray-100 rounded-xl shadow-sm">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-medium text-gray-700"><?= htmlspecialchars($review['customer_name']) ?></span>
                                        <span class="text-yellow-500 text-sm"><?= str_repeat('★', intval($review['rating'])) ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                    <p class="text-xs text-gray-400 mt-1"><?= formatDate($review['created_at'], 'M j, Y g:i A') ?></p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 italic">No reviews yet.</p>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
