<div class="bg-gray-50 py-10 min-h-screen">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-md p-6">
            
            <!-- Back Button -->
            <div class="mb-4">
                <a href="<?= BASE_URL ?>/provider/services.php" class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600 transition">
                    <!-- Back Arrow Icon -->
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Services
                </a>
            </div>

            <h1 class="text-2xl font-bold mb-6 text-gray-800">Edit Service</h1>
            <?= displayFlashMessage() ?>

            <form method="POST" action="<?= BASE_URL ?>/provider/services.php?action=update&id=<?= $existing_service->id ?>" class="space-y-6">
                <input type="hidden" name="action" value="update">

                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700">Service Title</label>
                    <input type="text" id="title" name="title" class="form-input mt-1 w-full" value="<?= htmlspecialchars($existing_service->title) ?>" required>
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="4" class="form-input mt-1 w-full" required><?= htmlspecialchars($existing_service->description) ?></textarea>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700">Category</label>
                    <select id="category_id" name="category_id" class="form-select mt-1 w-full" required>
                        <option value="">Select Category</option>
                        <?php while ($cat = $categories->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $cat['id'] ?>" <?= $existing_service->category_id == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700">Price</label>
                    <input type="number" id="price" name="price" class="form-input mt-1 w-full" step="0.01" value="<?= $existing_service->price ?>" required>
                </div>

                <div>
                    <label for="price_type" class="block text-sm font-semibold text-gray-700">Price Type</label>
                    <select id="price_type" name="price_type" class="form-select mt-1 w-full" required>
                        <option value="fixed" <?= $existing_service->price_type === 'fixed' ? 'selected' : '' ?>>Fixed Price</option>
                        <option value="hourly" <?= $existing_service->price_type === 'hourly' ? 'selected' : '' ?>>Hourly Rate</option>
                        <option value="starting_at" <?= $existing_service->price_type === 'starting_at' ? 'selected' : '' ?>>Starting At</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="<?= BASE_URL ?>/provider/services.php"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm rounded hover:bg-gray-300 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                        Update Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
