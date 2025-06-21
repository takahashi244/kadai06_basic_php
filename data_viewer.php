<!DOCTYPE html>
<html lang="ja">
<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>データ確認</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .data-section {
            margin-bottom: 2rem;
        }
        .json-data {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff5f5;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #ffe5e5;
        }        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #ff6b6b;
        }
    </style>
</head>
<body>    <header>
        <div class="container">
            <h1>📊 データ確認ページ</h1>
            <p class="subtitle">レビューデータ管理</p>
        </div>
    </header>

    <main class="container">
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="index.php" class="btn-primary">メインページに戻る</a>
            <a href="post_review.php" class="btn-secondary">レビュー投稿</a>
        </div>

        <?php
        require_once 'includes/DataManager.php';
        $dataManager = new DataManager();
        
        try {
            // 統計情報の取得
            $stats = $dataManager->getReviewStats();
            $students = $dataManager->getAllStudents();
            $reviews = $dataManager->getAllReviews();
            $universities = $dataManager->getUniversities();
            $departments = $dataManager->getDepartments();
            
            echo '<section class="data-section">';
            echo '<h2>📈 統計情報</h2>';
            echo '<div class="stats-grid">';
            echo '<div class="stat-card">';
            echo '<div class="stat-value">' . $stats['total_reviews'] . '</div>';
            echo '<div>総レビュー数</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-value">' . count($students) . '</div>';
            echo '<div>大学生数</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-value">' . count($universities) . '</div>';
            echo '<div>大学数</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-value">' . $stats['overall_avg'] . '</div>';
            echo '<div>平均評価</div>';
            echo '</div>';
            echo '</div>';
            
            echo '<div style="margin-top: 1rem;">';
            echo '<p><strong>話しやすさ平均:</strong> ' . $stats['avg_friendliness'] . '/5</p>';
            echo '<p><strong>参考度平均:</strong> ' . $stats['avg_helpfulness'] . '/5</p>';
            echo '<p><strong>ワクワク度平均:</strong> ' . $stats['avg_excitement'] . '/5</p>';
            echo '<p><strong>時間正確性平均:</strong> ' . $stats['avg_punctuality'] . '/5</p>';
            echo '</div>';
            echo '</section>';
              // 大学生データの表示
            echo '<section class="data-section">';
            echo '<h2>👨‍🎓 登録大学生一覧</h2>';
            echo '<p><strong>登録者数:</strong> ' . count($students) . '名</p>';
            echo '<div class="json-data">' . htmlspecialchars(json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</div>';
            echo '</section>';
              // レビューデータの表示
            echo '<section class="data-section">';
            echo '<h2>📝 投稿されたレビュー</h2>';
            echo '<p><strong>レビュー総数:</strong> ' . count($reviews) . '件</p>';
            echo '<div class="json-data">' . htmlspecialchars(json_encode($reviews, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</div>';
            echo '</section>';
              } catch (Exception $e) {
            echo '<section class="data-section">';
            echo '<div class="error-messages">';
            echo '<h3>❌ エラーが発生しました</h3>';
            echo '<p>データの読み込みに失敗しました。</p>';
            echo '<p>しばらく時間をおいてから再度お試しください。</p>';
            echo '</div>';
            echo '</section>';
        }        ?>

    </main>    <footer>
        <div class="container">
            <p>&copy; 2025 高校生・大学生マッチングアプリ</p>
        </div>
    </footer>
</body>
</html>
