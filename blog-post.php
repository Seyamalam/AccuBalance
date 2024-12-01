<?php
require_once 'config.php';

// Get post by slug
$slug = $conn->real_escape_string($_GET['slug']);
$query = "SELECT p.*, u.username as author_name 
          FROM blog_posts p 
          LEFT JOIN users u ON p.author_id = u.id 
          WHERE p.slug = '$slug' AND p.status = 'published'";
$post = $conn->query($query)->fetch_assoc();

if (!$post) {
    header('Location: blog.php');
    exit();
}

// Update view count
$conn->query("UPDATE blog_posts SET views = views + 1 WHERE id = " . $post['id']);

// Get related posts
$category = $conn->real_escape_string($post['category']);
$related_query = "SELECT * FROM blog_posts 
                  WHERE category = '$category' 
                  AND id != " . $post['id'] . "
                  AND status = 'published'
                  LIMIT 3";
$related_posts = $conn->query($related_query);

// Get comments
$comments_query = "SELECT c.*, u.username 
                  FROM blog_comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = " . $post['id'] . "
                  AND c.status = 'approved'
                  ORDER BY c.created_at DESC";
$comments = $conn->query($comments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post['title']; ?> - AccuBalance Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.24.1/themes/prism.css">
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.24.1/prism.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .blog-content {
            line-height: 1.8;
        }

        .blog-content h1 { font-size: 2.5rem; margin: 2rem 0 1rem; }
        .blog-content h2 { font-size: 2rem; margin: 1.8rem 0 0.9rem; }
        .blog-content h3 { font-size: 1.5rem; margin: 1.6rem 0 0.8rem; }
        .blog-content p { margin-bottom: 1.2rem; }
        .blog-content ul, .blog-content ol { margin: 1.2rem 0; padding-left: 2rem; }
        .blog-content li { margin-bottom: 0.5rem; }
        .blog-content blockquote {
            border-left: 4px solid #3B82F6;
            padding-left: 1rem;
            margin: 1.5rem 0;
            color: #4B5563;
        }

        .social-share-button {
            transition: all 0.3s ease;
        }

        .social-share-button:hover {
            transform: translateY(-3px);
        }

        .comment-animation {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/public-nav.php'; ?>

    <div class="max-w-4xl mx-auto px-4 py-12">
        <!-- Article Header -->
        <header class="text-center mb-12" data-aos="fade-up">
            <div class="mb-4">
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                    <?php echo $post['category']; ?>
                </span>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4"><?php echo $post['title']; ?></h1>
            <div class="flex items-center justify-center space-x-4 text-gray-600">
                <span>
                    <i class="fas fa-user mr-2"></i>
                    <?php echo $post['author_name']; ?>
                </span>
                <span>
                    <i class="fas fa-calendar mr-2"></i>
                    <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                </span>
                <span>
                    <i class="fas fa-eye mr-2"></i>
                    <?php echo $post['views']; ?> views
                </span>
            </div>
        </header>

        <!-- Article Content -->
        <article class="bg-white rounded-lg shadow-lg p-8 mb-12" data-aos="fade-up" data-aos-delay="100">
            <div class="blog-content prose max-w-none" id="blogContent">
                <?php echo $post['content']; ?>
            </div>

            <!-- Tags -->
            <div class="mt-8 pt-8 border-t">
                <div class="flex flex-wrap gap-2">
                    <?php foreach (json_decode($post['tags']) as $tag): ?>
                        <a href="blog.php?tag=<?php echo urlencode($tag); ?>" 
                           class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm hover:bg-gray-200 transition-colors">
                            #<?php echo $tag; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Social Share -->
            <div class="mt-8 pt-8 border-t">
                <h3 class="text-lg font-bold mb-4">Share this article</h3>
                <div class="flex space-x-4">
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($current_url); ?>&text=<?php echo urlencode($post['title']); ?>" 
                       target="_blank"
                       class="social-share-button bg-blue-400 text-white px-4 py-2 rounded-lg">
                        <i class="fab fa-twitter mr-2"></i>Twitter
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?url=<?php echo urlencode($current_url); ?>&title=<?php echo urlencode($post['title']); ?>" 
                       target="_blank"
                       class="social-share-button bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fab fa-linkedin mr-2"></i>LinkedIn
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($current_url); ?>" 
                       target="_blank"
                       class="social-share-button bg-blue-600 text-white px-4 py-2 rounded-lg">
                        <i class="fab fa-facebook mr-2"></i>Facebook
                    </a>
                </div>
            </div>
        </article>

        <!-- Comments Section -->
        <section class="bg-white rounded-lg shadow-lg p-8 mb-12" data-aos="fade-up" data-aos-delay="200">
            <h2 class="text-2xl font-bold mb-6">Comments</h2>

            <?php if ($is_logged_in): ?>
                <!-- Comment Form -->
                <form method="POST" class="mb-8">
                    <input type="hidden" name="action" value="add_comment">
                    <textarea name="content" rows="4" 
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Add your comment..."></textarea>
                    <button type="submit" 
                            class="mt-2 bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                        Post Comment
                    </button>
                </form>
            <?php else: ?>
                <div class="bg-gray-50 p-4 rounded-lg mb-8">
                    <p class="text-gray-600">
                        Please <a href="login.php" class="text-blue-500">login</a> to post a comment.
                    </p>
                </div>
            <?php endif; ?>

            <!-- Comments List -->
            <div class="space-y-6">
                <?php while ($comment = $comments->fetch_assoc()): ?>
                    <div class="comment-animation bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center">
                                <img src="https://www.gravatar.com/avatar/<?php echo md5($comment['username']); ?>?d=mp" 
                                     alt="<?php echo $comment['username']; ?>"
                                     class="w-8 h-8 rounded-full mr-3">
                                <div>
                                    <h4 class="font-medium"><?php echo $comment['username']; ?></h4>
                                    <p class="text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($comment['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <?php if ($is_logged_in && $_SESSION['user_id'] == $comment['user_id']): ?>
                                <button onclick="deleteComment(<?php echo $comment['id']; ?>)"
                                        class="text-red-500 hover:text-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-700"><?php echo $comment['content']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Related Posts -->
        <section data-aos="fade-up" data-aos-delay="300">
            <h2 class="text-2xl font-bold mb-6">Related Articles</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <?php while ($related = $related_posts->fetch_assoc()): ?>
                    <a href="blog-post.php?slug=<?php echo $related['slug']; ?>" 
                       class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <?php if ($related['featured_image']): ?>
                            <img src="<?php echo $related['featured_image']; ?>" 
                                 alt="<?php echo $related['title']; ?>"
                                 class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="font-bold text-lg mb-2"><?php echo $related['title']; ?></h3>
                            <p class="text-gray-600 text-sm">
                                <?php echo substr($related['excerpt'], 0, 100) . '...'; ?>
                            </p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        // Convert markdown to HTML
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.getElementById('blogContent');
            content.innerHTML = marked(content.textContent);
            Prism.highlightAll();
        });

        // Handle comment deletion
        function deleteComment(commentId) {
            if (confirm('Are you sure you want to delete this comment?')) {
                fetch('api/comments.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ comment_id: commentId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting comment');
                    }
                });
            }
        }
    </script>
</body>
</html> 