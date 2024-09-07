<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <h4>Menu</h4>
        <ul class="nav flex-column">
            <!-- ニュース管理を一番上に追加 -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('news_articles.management') ? 'active' : '' }}" href="{{ route('news_articles.management') }}">
                    <i class="fas fa-newspaper"></i> ニュース管理
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('news_articles.index') ? 'active' : '' }}" href="{{ route('news_articles.index') }}">
                    <i class="fas fa-table"></i> News Articles
                </a>
            </li>
            <!-- 他のメニュー項目も同様に調整 -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('x_posts.index') ? 'active' : '' }}" href="{{ route('x_posts.index') }}">
                    <i class="fas fa-table"></i> X Posts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('x_vectors.index') ? 'active' : '' }}" href="{{ route('x_vectors.index') }}">
                    <i class="fas fa-table"></i> X Vectors
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('x_prompts.index') ? 'active' : '' }}" href="{{ route('x_prompts.index') }}">
                    <i class="fas fa-table"></i> X Prompts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('x_generated_posts.index') ? 'active' : '' }}" href="{{ route('x_generated_posts.index') }}">
                    <i class="fas fa-table"></i> X Generated Posts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('x_replies.index') ? 'active' : '' }}" href="{{ route('x_replies.index') }}">
                    <i class="fas fa-table"></i> X Replies
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('x_feedbacks.index') ? 'active' : '' }}" href="{{ route('x_feedbacks.index') }}">
                    <i class="fas fa-table"></i> X Feedbacks
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('code_list.index') ? 'active' : '' }}" href="{{ route('code_list.index') }}">
                    <i class="fas fa-table"></i> Code List
                </a>
            </li>
        </ul>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
