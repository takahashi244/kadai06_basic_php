<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>高校生・大学生マッチング - レビュー一覧 (SQLなし版)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>高校生・大学生マッチング</h1>
            <p class="subtitle">WEB面談レビューシステム (SQLなし版)</p>
            <p class="version-note">📂 JSONファイルベース</p>
        </div>
    </header>

    <main class="container">
        <!-- 検索・絞り込みフォーム -->
        <section class="search-section">
            <h2>レビューを検索・絞り込み</h2>
            <form method="GET" action="index.php" class="search-form">
                <div class="search-row">
                    <div class="form-group">
                        <label for="university">大学名</label>
                        <select name="university" id="university">
                            <option value="">すべて</option>
                            <?php
                            require_once 'includes/DataManager.php';
                            $dataManager = new DataManager();
                            
                            try {
                                $universities = $dataManager->getUniversities();
                                foreach ($universities as $university) {
                                    $selected = (isset($_GET['university']) && $_GET['university'] === $university) ? 'selected' : '';
                                    echo "<option value=\"{$university}\" $selected>{$university}</option>";
                                }
                            } catch (Exception $e) {
                                echo "<option value=\"\">エラー: データ読み込み失敗</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="department">学部</label>
                        <select name="department" id="department">
                            <option value="">すべて</option>
                            <?php
                            try {
                                $departments = $dataManager->getDepartments();
                                foreach ($departments as $department) {
                                    $selected = (isset($_GET['department']) && $_GET['department'] === $department) ? 'selected' : '';
                                    echo "<option value=\"{$department}\" $selected>{$department}</option>";
                                }
                            } catch (Exception $e) {
                                echo "<option value=\"\">エラー: データ読み込み失敗</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="rating">評価</label>
                        <select name="rating" id="rating">
                            <option value="">すべて</option>
                            <option value="4.5" <?= (isset($_GET['rating']) && $_GET['rating'] === '4.5') ? 'selected' : '' ?>>4.5以上</option>
                            <option value="4.0" <?= (isset($_GET['rating']) && $_GET['rating'] === '4.0') ? 'selected' : '' ?>>4.0以上</option>
                            <option value="3.5" <?= (isset($_GET['rating']) && $_GET['rating'] === '3.5') ? 'selected' : '' ?>>3.5以上</option>
                            <option value="3.0" <?= (isset($_GET['rating']) && $_GET['rating'] === '3.0') ? 'selected' : '' ?>>3.0以上</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="period">投稿日</label>
                        <select name="period" id="period">
                            <option value="">すべて</option>
                            <option value="7" <?= (isset($_GET['period']) && $_GET['period'] === '7') ? 'selected' : '' ?>>1週間以内</option>
                            <option value="30" <?= (isset($_GET['period']) && $_GET['period'] === '30') ? 'selected' : '' ?>>1ヶ月以内</option>
                            <option value="90" <?= (isset($_GET['period']) && $_GET['period'] === '90') ? 'selected' : '' ?>>3ヶ月以内</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-search">検索</button>
                        <a href="index.php" class="btn-reset">リセット</a>
                    </div>
                </div>
            </form>
        </section>

        <!-- レビュー投稿ボタン -->
        <section class="action-section">
            <a href="post_review.php" class="btn-post">新しいレビューを投稿</a>
            <a href="data_viewer.php" class="btn-secondary">データ確認</a>
        </section>

        <!-- レビュー一覧 -->
        <section class="reviews-section">
            <h2>レビュー一覧</h2>
            
            <?php
            try {
                // フィルタ条件を設定
                $filters = [];
                if (!empty($_GET['university'])) {
                    $filters['university'] = $_GET['university'];
                }
                if (!empty($_GET['department'])) {
                    $filters['department'] = $_GET['department'];
                }
                if (!empty($_GET['rating'])) {
                    $filters['rating'] = $_GET['rating'];
                }
                if (!empty($_GET['period'])) {
                    $filters['period'] = $_GET['period'];
                }

                // レビューデータの取得
                $reviews = $dataManager->getReviewsWithStudents($filters);

                if (empty($reviews)) {
                    echo '<p class="no-results">該当するレビューが見つかりませんでした。</p>';
                } else {
                    foreach ($reviews as $review) {
                        $gradeText = $review['grade'] == 5 ? '院生' : $review['grade'] . '年';
                        $reviewerGradeText = $review['reviewer_grade'] . '年';
                        $reviewDate = date('Y年n月j日', strtotime($review['review_date']));
                        $avgRating = round($review['avg_rating'], 1);
                        
                        echo '<div class="review-card">';
                        echo '<div class="review-header">';
                        echo "<h3>{$review['name']} さん</h3>";
                        echo "<div class=\"student-info\">";
                        echo "<span class=\"university\">{$review['university']}</span>";
                        echo "<span class=\"department\">{$review['department']}</span>";
                        echo "<span class=\"grade\">{$gradeText}</span>";
                        echo "</div>";
                        echo '</div>';
                        
                        echo '<div class="review-ratings">';
                        echo '<div class="rating-summary">';
                        echo "<span class=\"avg-rating\">総合評価: {$avgRating}</span>";
                        echo '<div class="stars">' . str_repeat('★', floor($avgRating)) . str_repeat('☆', 5 - floor($avgRating)) . '</div>';
                        echo '</div>';
                        
                        echo '<div class="rating-details">';
                        echo "<div class=\"rating-item\"><span>話しやすさ:</span> {$review['friendliness']}/5</div>";
                        echo "<div class=\"rating-item\"><span>参考になった度:</span> {$review['helpfulness']}/5</div>";
                        echo "<div class=\"rating-item\"><span>ワクワク度:</span> {$review['excitement']}/5</div>";
                        echo "<div class=\"rating-item\"><span>時間の正確性:</span> {$review['punctuality']}/5</div>";
                        echo '</div>';
                        echo '</div>';
                        
                        if (!empty($review['comment'])) {
                            echo '<div class="review-comment">';
                            echo '<h4>コメント</h4>';
                            echo '<p>' . nl2br(htmlspecialchars($review['comment'])) . '</p>';
                            echo '</div>';
                        }
                        
                        echo '<div class="review-footer">';
                        echo "<div class=\"reviewer-info\">";
                        if (!empty($review['reviewer_nickname'])) {
                            echo "<span class=\"nickname\">{$review['reviewer_nickname']}</span>";
                        } else {
                            echo "<span class=\"nickname\">匿名</span>";
                        }
                        echo "<span class=\"school\">{$review['reviewer_school']}</span>";
                        echo "<span class=\"grade\">{$reviewerGradeText}</span>";
                        echo "</div>";
                        echo "<div class=\"review-date\">{$reviewDate}</div>";
                        echo '</div>';
                        echo '</div>';
                    }
                }
            } catch (Exception $e) {
                echo '<p class="error">レビューの取得中にエラーが発生しました。</p>';
                echo '<p class="error">エラー内容: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 高校生・大学生マッチングアプリ (SQLなし版)</p>
            <p class="comparison-link">
                <a href="../kadai06_basic_php/">SQL版と比較</a>
            </p>
        </div>
    </footer>
</body>
</html>
