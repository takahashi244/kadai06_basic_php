<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>データ確認 - SQLなし版</title>
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
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #ff6b6b;
        }
        .comparison-section {
            background: #e3f2fd;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #1976d2;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>📊 データ確認ページ</h1>
            <p class="subtitle">JSONファイルベースのデータ管理 (SQLなし版)</p>
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
            echo '<h2>👨‍🎓 大学生データ (students.json)</h2>';
            echo '<p><strong>ファイル場所:</strong> data/students.json</p>';
            echo '<p><strong>データ件数:</strong> ' . count($students) . '件</p>';
            echo '<div class="json-data">' . htmlspecialchars(json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</div>';
            echo '</section>';
            
            // レビューデータの表示
            echo '<section class="data-section">';
            echo '<h2>📝 レビューデータ (reviews.json)</h2>';
            echo '<p><strong>ファイル場所:</strong> data/reviews.json</p>';
            echo '<p><strong>データ件数:</strong> ' . count($reviews) . '件</p>';
            echo '<div class="json-data">' . htmlspecialchars(json_encode($reviews, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</div>';
            echo '</section>';
            
        } catch (Exception $e) {
            echo '<section class="data-section">';
            echo '<div class="error-messages">';
            echo '<h3>❌ エラーが発生しました</h3>';
            echo '<p>データファイルの読み込みに失敗しました。</p>';
            echo '<p>エラー内容: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p>以下を確認してください：</p>';
            echo '<ul>';
            echo '<li>data/students.json ファイルが存在するか</li>';
            echo '<li>data/reviews.json ファイルが存在するか</li>';
            echo '<li>ファイルの読み書き権限があるか</li>';
            echo '<li>JSONファイルの形式が正しいか</li>';
            echo '</ul>';
            echo '</div>';
            echo '</section>';
        }
        ?>

        <!-- SQL版との比較セクション -->
        <section class="comparison-section">
            <h2>🔄 SQL版との比較</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1rem;">
                <div>
                    <h3>📂 SQLなし版 (現在のページ)</h3>
                    <ul>
                        <li>📄 JSONファイルでデータ管理</li>
                        <li>🔧 データベース設定不要</li>
                        <li>💾 ファイルベースの保存</li>
                        <li>🏃‍♂️ セットアップが簡単</li>
                        <li>📊 データはテキストファイルで確認可能</li>
                        <li>⚠️ 大量データには不向き</li>
                        <li>🔒 同時アクセス制御が困難</li>
                    </ul>
                </div>
                <div>
                    <h3>🗄️ SQL版</h3>
                    <ul>
                        <li>💾 MySQLデータベースでデータ管理</li>
                        <li>🛠️ データベースサーバー必要</li>
                        <li>⚡ 高速なデータ検索・操作</li>
                        <li>🏗️ 複雑なクエリに対応</li>
                        <li>📈 大量データの処理が得意</li>
                        <li>🔄 トランザクション処理</li>
                        <li>👥 複数ユーザーの同時アクセス対応</li>
                    </ul>
                </div>
            </div>
            
            <div style="margin-top: 2rem; text-align: center;">
                <h4>🚀 比較してみよう！</h4>
                <p>
                    <a href="../kadai06_basic_php/" class="btn-primary">SQL版を確認</a>
                    <a href="index.php" class="btn-secondary">SQLなし版に戻る</a>
                </p>
            </div>
        </section>

        <!-- ファイル構造の説明 -->
        <section class="data-section">
            <h2>📁 ファイル構造</h2>
            <div class="json-data">kadai06_no_sql/
├── index.php              (メインページ)
├── post_review.php        (レビュー投稿)
├── data_viewer.php        (このページ)
├── css/
│   └── style.css         (スタイルシート)
├── includes/
│   └── DataManager.php   (データ管理クラス)
└── data/
    ├── students.json     (大学生データ)
    └── reviews.json      (レビューデータ)</div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 高校生・大学生マッチングアプリ (SQLなし版)</p>
        </div>
    </footer>
</body>
</html>
