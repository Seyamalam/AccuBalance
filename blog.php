<?php
require_once 'config.php';

// Get blog posts with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;

$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : null;
$tag = isset($_GET['tag']) ? $conn->real_escape_string($_GET['tag']) : null;

// Build query
$where = "WHERE status = 'published'";
if ($category) {
    $where .= " AND category = '$category'";
}
if ($tag) {
    $where .= " AND JSON_CONTAINS(tags, '\"$tag\"', '$')";
}

$query = "SELECT * FROM blog_posts $where 
          ORDER BY created_at DESC LIMIT $offset, $per_page";
$posts = $conn->query($query);

// Get total posts for pagination
$total_query = "SELECT COUNT(*) as total FROM blog_posts $where";
$total_posts = $conn->query($total_query)->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $per_page);

// Get categories and tags
$categories_query = "SELECT DISTINCT category FROM blog_posts WHERE status = 'published'";
$categories = $conn->query($categories_query);

$tags_query = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(tags, '$[*]')) as tag 
               FROM blog_posts, JSON_TABLE(tags, '$[*]' COLUMNS (tag VARCHAR(50) PATH '$')) t
               WHERE status = 'published'";
$tags = $conn->query($tags_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Insights Blog - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        .blog-card {
            transition: all 0.3s ease;
        }

        .blog-card:hover {
            transform: translateY(-10px);
        }

        .category-badge {
            transition: all 0.3s ease;
        }

        .category-badge:hover {
            transform: scale(1.05);
        }

        .tag-pill {
            transition: all 0.3s ease;
        }

        .tag-pill:hover {
            transform: translateX(5px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/public-nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Header -->
        <div class="text-center mb-12" data-aos="fade-up">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Financial Insights</h1>
            <p class="text-xl text-gray-600">Expert advice and tips for better financial management</p>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Categories -->
                <div class="bg-white rounded-lg shadow p-6 mb-6" data-aos="fade-right">
                    <h2 class="text-xl font-bold mb-4">Categories</h2>
                    <div class="space-y-2">
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <a href="?category=<?php echo urlencode($category['category']); ?>" 
                               class="category-badge block px-4 py-2 rounded-lg bg-gray-100 hover:bg-blue-500 hover:text-white transition-all">
                                <?php echo $category['category']; ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Popular Tags -->
                <div class="bg-white rounded-lg shadow p-6" data-aos="fade-right" data-aos-delay="100">
                    <h2 class="text-xl font-bold mb-4">Popular Tags</h2>
                    <div class="flex flex-wrap gap-2">
                        <?php while ($tag = $tags->fetch_assoc()): ?>
                            <a href="?tag=<?php echo urlencode($tag['tag']); ?>" 
                               class="tag-pill inline-block px-3 py-1 bg-gray-100 text-sm rounded-full hover:bg-blue-500 hover:text-white transition-all">
                                #<?php echo $tag['tag']; ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Blog Posts Grid -->
            <div class="lg:col-span-3 grid md:grid-cols-2 gap-6">
                <?php 
                $delay = 0;
                while ($post = $posts->fetch_assoc()): 
                ?>
                    <article class="blog-card bg-white rounded-lg shadow overflow-hidden" 
                             data-aos="fade-up" 
                             data-aos-delay="<?php echo $delay; ?>">
                        <?php if ($post['featured_image']): ?>
                            <img src="<?php echo $post['featured_image']; ?>" 
                                 alt="<?php echo $post['title']; ?>"
                                 class="w-full h-48 object-cover">
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">
                                    <?php echo $post['category']; ?>
                                </span>
                                <span class="text-gray-500 text-sm ml-4">
                                    <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                </span>
                            </div>
                            
                            <h2 class="text-xl font-bold mb-2">
                                <a href="blog-post.php?slug=<?php echo $post['slug']; ?>" 
                                   class="text-gray-900 hover:text-blue-600">
                                    <?php echo $post['title']; ?>
                                </a>
                            </h2>
                            
                            <p class="text-gray-600 mb-4">
                                <?php echo $post['excerpt']; ?>
                            </p>
                            
                            <a href="blog-post.php?slug=<?php echo $post['slug']; ?>" 
                               class="text-blue-500 hover:text-blue-600 font-medium">
                                Read More <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </article>
                <?php 
                    $delay += 100;
                endwhile; 
                ?>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-12 flex justify-center">
                <div class="flex space-x-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?><?php echo $tag ? '&tag=' . urlencode($tag) : ''; ?>" 
                           class="px-4 py-2 rounded-lg <?php echo $page === $i ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html> 