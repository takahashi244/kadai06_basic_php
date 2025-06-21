<?php
/**
 * JSONファイルベースのデータ管理クラス
 * SQLを使わずにファイルでデータを管理
 * 作成日: 2025年6月21日
 */

class DataManager {
    private $dataDir;    public function __construct() {
        $this->dataDir = dirname(__DIR__) . '/data/';
        // デバッグ: パス確認
        if (!is_dir($this->dataDir)) {
            echo "<!-- データディレクトリが見つかりません: " . $this->dataDir . " -->";
        } else {
            echo "<!-- データディレクトリ確認OK: " . $this->dataDir . " -->";
        }
    }
      /**
     * JSONファイルからデータを読み込み
     */
    private function loadJSON($filename) {
        $filepath = $this->dataDir . $filename;
        
        // デバッグ情報
        if (!file_exists($filepath)) {
            echo "<!-- ファイルが見つかりません: $filepath -->";
            return [];
        }
        
        $json = file_get_contents($filepath);
        if ($json === false) {
            echo "<!-- ファイル読み込み失敗: $filepath -->";
            return [];
        }
        
        $data = json_decode($json, true);
        if ($data === null) {
            echo "<!-- JSON解析失敗: $filepath -->";
            return [];
        }
        
        return $data;
    }
    
    /**
     * JSONファイルにデータを保存
     */
    private function saveJSON($filename, $data) {
        $filepath = $this->dataDir . $filename;
        
        // ディレクトリが存在しない場合は作成
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
        
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($filepath, $json) !== false;
    }
    
    /**
     * 全大学生データを取得
     */
    public function getAllStudents() {
        return $this->loadJSON('students.json');
    }
    
    /**
     * 大学生データをIDで取得
     */
    public function getStudentById($id) {
        $students = $this->getAllStudents();
        foreach ($students as $student) {
            if ($student['id'] == $id) {
                return $student;
            }
        }
        return null;
    }
    
    /**
     * 大学名の一覧を取得
     */
    public function getUniversities() {
        $students = $this->getAllStudents();
        $universities = [];
        foreach ($students as $student) {
            if (!in_array($student['university'], $universities)) {
                $universities[] = $student['university'];
            }
        }
        sort($universities);
        return $universities;
    }
    
    /**
     * 学部の一覧を取得
     */
    public function getDepartments() {
        $students = $this->getAllStudents();
        $departments = [];
        foreach ($students as $student) {
            if (!in_array($student['department'], $departments)) {
                $departments[] = $student['department'];
            }
        }
        sort($departments);
        return $departments;
    }
    
    /**
     * 全レビューデータを取得
     */
    public function getAllReviews() {
        return $this->loadJSON('reviews.json');
    }
    
    /**
     * レビューデータに大学生情報をJOINして取得
     */
    public function getReviewsWithStudents($filters = []) {
        $reviews = $this->getAllReviews();
        $students = $this->getAllStudents();
        $result = [];
        
        foreach ($reviews as $review) {
            // 大学生情報を取得
            $student = null;
            foreach ($students as $s) {
                if ($s['id'] == $review['student_id']) {
                    $student = $s;
                    break;
                }
            }
            
            if ($student) {
                // レビューデータに大学生情報をマージ
                $reviewWithStudent = array_merge($review, [
                    'name' => $student['name'],
                    'university' => $student['university'],
                    'department' => $student['department'],
                    'grade' => $student['grade']
                ]);
                
                // 平均評価を計算
                $reviewWithStudent['avg_rating'] = 
                    ($review['friendliness'] + $review['helpfulness'] + 
                     $review['excitement'] + $review['punctuality']) / 4.0;
                
                // フィルタリング
                if ($this->matchesFilters($reviewWithStudent, $filters)) {
                    $result[] = $reviewWithStudent;
                }
            }
        }
        
        // ソート（最新順）
        usort($result, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return $result;
    }
    
    /**
     * フィルタ条件にマッチするかチェック
     */
    private function matchesFilters($review, $filters) {
        // 大学名フィルタ
        if (!empty($filters['university']) && $review['university'] !== $filters['university']) {
            return false;
        }
        
        // 学部フィルタ
        if (!empty($filters['department']) && $review['department'] !== $filters['department']) {
            return false;
        }
        
        // 評価フィルタ
        if (!empty($filters['rating'])) {
            $threshold = floatval($filters['rating']);
            if ($review['avg_rating'] < $threshold) {
                return false;
            }
        }
        
        // 期間フィルタ
        if (!empty($filters['period'])) {
            $days = intval($filters['period']);
            $cutoffDate = date('Y-m-d', strtotime("-{$days} days"));
            if ($review['review_date'] < $cutoffDate) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 新しいレビューを追加
     */
    public function addReview($reviewData) {
        $reviews = $this->getAllReviews();
        
        // 新しいIDを生成
        $maxId = 0;
        foreach ($reviews as $review) {
            if ($review['id'] > $maxId) {
                $maxId = $review['id'];
            }
        }
        
        $newReview = [
            'id' => $maxId + 1,
            'student_id' => intval($reviewData['student_id']),
            'reviewer_nickname' => $reviewData['reviewer_nickname'] ?: null,
            'reviewer_school' => $reviewData['reviewer_school'],
            'reviewer_grade' => intval($reviewData['reviewer_grade']),
            'friendliness' => intval($reviewData['friendliness']),
            'helpfulness' => intval($reviewData['helpfulness']),
            'excitement' => intval($reviewData['excitement']),
            'punctuality' => intval($reviewData['punctuality']),
            'comment' => $reviewData['comment'] ?: null,
            'review_date' => $reviewData['review_date'],
            'created_at' => date('Y-m-d\TH:i:s')
        ];
        
        $reviews[] = $newReview;
        
        return $this->saveJSON('reviews.json', $reviews);
    }
    
    /**
     * レビュー統計を取得
     */
    public function getReviewStats() {
        $reviews = $this->getAllReviews();
        
        if (empty($reviews)) {
            return [
                'total_reviews' => 0,
                'avg_friendliness' => 0,
                'avg_helpfulness' => 0,
                'avg_excitement' => 0,
                'avg_punctuality' => 0,
                'overall_avg' => 0
            ];
        }
        
        $totalFriendliness = 0;
        $totalHelpfulness = 0;
        $totalExcitement = 0;
        $totalPunctuality = 0;
        
        foreach ($reviews as $review) {
            $totalFriendliness += $review['friendliness'];
            $totalHelpfulness += $review['helpfulness'];
            $totalExcitement += $review['excitement'];
            $totalPunctuality += $review['punctuality'];
        }
        
        $count = count($reviews);
        $avgFriendliness = $totalFriendliness / $count;
        $avgHelpfulness = $totalHelpfulness / $count;
        $avgExcitement = $totalExcitement / $count;
        $avgPunctuality = $totalPunctuality / $count;
        
        return [
            'total_reviews' => $count,
            'avg_friendliness' => round($avgFriendliness, 2),
            'avg_helpfulness' => round($avgHelpfulness, 2),
            'avg_excitement' => round($avgExcitement, 2),
            'avg_punctuality' => round($avgPunctuality, 2),
            'overall_avg' => round(($avgFriendliness + $avgHelpfulness + $avgExcitement + $avgPunctuality) / 4, 2)
        ];
    }
}
?>
