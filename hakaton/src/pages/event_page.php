<?php
session_start();
use config\Database;
$db = Database::getConnection();

$event_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($event_id == 0) {
    echo "<script>alert('Мероприятие не найдено'); window.location='/';</script>";
    exit();
}

$eventQuery = mysqli_query($db, "SELECT * FROM `event` WHERE `event_id` = '$event_id'");
if (mysqli_num_rows($eventQuery) == 0) {
    echo "<script>alert('Мероприятие не найдено'); window.location='/';</script>";
    exit();
}

$event = mysqli_fetch_assoc($eventQuery);

$promoterQuery = mysqli_query($db, "SELECT * FROM `promoter` WHERE `promoter_id` = '{$event['promoter_id']}'");
$promoter = mysqli_fetch_assoc($promoterQuery);

$categoryQuery = mysqli_query($db, "SELECT * FROM `event_category` WHERE `event_category_id` = '{$event['event_category_id']}'");
$category = mysqli_fetch_assoc($categoryQuery);

$difficultyQuery = mysqli_query($db, "SELECT * FROM `difficulty` WHERE `difficulty_id` = '{$event['difficulty_id']}'");
$difficulty = mysqli_fetch_assoc($difficultyQuery);

$status_names = [
    1 => 'На модерации',
    2 => 'Приём заявок',
    3 => 'Идёт',
    4 => 'Завершено'
];

$status_colors = [
    1 => '#FFA500',
    2 => '#4CAF50',
    3 => '#2196F3',
    4 => '#9E9E9E'
];

$isPromoter = isset($_SESSION['register_role']) && $_SESSION['register_role'] == '2';
$isCoordinator = isset($_SESSION['register_role']) && $_SESSION['register_role'] == '3';

if (!$isPromoter && !$isCoordinator) {
    if ($event['status_id'] == 1 || $event['status_id'] == 3) {
        echo "<script>alert('Мероприятие не доступно'); window.location='/';</script>";
        exit();
    }
}

$isRegistered = false;
if (isset($_SESSION['register_id']) && $_SESSION['register_role'] == '1') {
    $userQuery = mysqli_query($db, "SELECT `user_id` FROM `user` WHERE `register_id` = '{$_SESSION['register_id']}'");
    $user = mysqli_fetch_assoc($userQuery);
    $user_id = $user['user_id'];
    
    $checkParticipation = mysqli_query($db, "SELECT * FROM `participation` WHERE `user_id` = '$user_id' AND `event_id` = '$event_id'");
    if (mysqli_num_rows($checkParticipation) > 0) {
        $isRegistered = true;
        $participation = mysqli_fetch_assoc($checkParticipation);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['event_name']) ?> | EventHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            line-height: 1.4;
            min-height: 100vh;
            background: linear-gradient(90deg, #DFE4FF 0%, #9FBFFF 50%, #DFE4FF 100%);
            overflow-x: hidden;
        }

        .header {
            background-color: #9FBFFF;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(90deg, #0022CD, #A100FF);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.02em;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            font-weight: 500;
            color: #1A1F2E;
            transition: 0.2s;
            font-size: 1rem;
        }

        .nav-links a:hover {
            color: #0022CD;
        }

        .profile-link {
            background: #F40BA6;
            padding: 10px 24px;
            border-radius: 40px;
            color: white !important;
            font-weight: 600;
            transition: 0.2s;
            opacity: 0.8;
        }

        .profile-link:hover {
            opacity: 1;
            transform: scale(0.96);
            background: #0022CD;
        }

        .logout-link {
            color: #cd001f !important;
            font-weight: 600;
            margin-left: 10px;
        }

        .logout-link:hover {
            text-decoration: underline;
        }

        .bg-circles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .circle {
            position: absolute;
            border: 40px solid rgba(99, 102, 241, 0.1);
            border-radius: 50%;
        }

        .c1 { width: 600px; height: 600px; top: -100px; right: -200px; }
        .c2 { width: 400px; height: 400px; bottom: 10%; left: -150px; }

        .main-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .event-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .event-header {
            background: linear-gradient(135deg, #7C3AED, #6366F1);
            padding: 30px;
            color: white;
        }

        .event-header h1 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            background: <?= $status_colors[$event['status_id']] ?>;
            color: white;
        }

        .event-content {
            padding: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
        }

        .info-item strong {
            display: block;
            color: #6366F1;
            margin-bottom: 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn {
            background: #F40BA6;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: #0022CD;
            transform: scale(0.98);
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .reviews-section {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .reviews-section h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .review-card {
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin: 15px 0;
            border-radius: 12px;
            transition: 0.2s;
        }

        .review-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .reviewer-name {
            font-weight: 600;
            color: #6366F1;
        }

        .review-stars {
            color: #FFD700;
            font-size: 18px;
        }

        .review-text {
            color: #555;
            line-height: 1.6;
            margin-top: 10px;
        }

        .form-section {
            background: white;
            border-radius: 24px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-section h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #6366F1;
        }

        .alert {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-warning {
            background: #fff3e0;
            color: #f57c00;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #6366F1;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="bg-circles">
        <div class="circle c1"></div>
        <div class="circle c2"></div>
    </div>

    <header class="header">
        <a href="/" class="logo">EventHub</a>
        <div class="nav-links">
            <a href="/top_users">Топ участников</a>
            <a href="/events">Мероприятия</a>
            <a href="/organizers">Организаторы</a>
            
            <?php if (isset($_SESSION['register_role']) && $_SESSION['register_role'] == '2'): ?>
                <a href="/event_new" class="profile-link">Создать мероприятие</a>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['register_id'])): ?>
                <a href="/profile" class="profile-link">Личный кабинет</a>
                <a href="/logout" class="logout-link">Выйти</a>
            <?php else: ?>
                <a href="/login" class="profile-link">Войти</a>
                <a href="/reg" class="profile-link">Регистрация</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="main-container">
        <div class="event-card">
            <div class="event-header">
                <span class="status-badge"><?= $status_names[$event['status_id']] ?></span>
                <h1><?= htmlspecialchars($event['event_name']) ?></h1>
                <p><?= htmlspecialchars($event['event_descr']) ?></p>
            </div>
            
            <div class="event-content">
                <div class="info-grid">
                    <div class="info-item">
                        <strong>📅 Дата проведения</strong>
                        <span><?= date('d.m.Y H:i', strtotime($event['event_date'])) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>📍 Формат</strong>
                        <span><?= $event['event_format'] == 'online' ? '🖥️ Онлайн' : '🏢 Оффлайн' ?></span>
                    </div>
                    <div class="info-item">
                        <strong>🏷️ Категория</strong>
                        <span><?= htmlspecialchars($category['event_category_name']) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>⚡ Сложность</strong>
                        <span><?= htmlspecialchars($difficulty['difficulty_name']) ?> (коэф. <?= $difficulty['difficulty_coefficient'] ?>)</span>
                    </div>
                    <div class="info-item">
                        <strong>🎯 Базовый балл</strong>
                        <span><?= $event['base_score'] ?> баллов</span>
                    </div>
                    <div class="info-item">
                        <strong>🏆 Призовые баллы</strong>
                        <span>1 место: <?= $event['firstp_score'] ?> | 2 место: <?= $event['secondp_score'] ?> | 3 место: <?= $event['thirdp_score'] ?></span>
                    </div>
                    <div class="info-item">
                        <strong>🎁 Бонус</strong>
                        <span><?= $event['event_bonus'] ? htmlspecialchars($event['event_bonus']) : 'Нет' ?></span>
                    </div>
                    <div class="info-item">
                        <strong>👤 Организатор</strong>
                        <span><?= htmlspecialchars($promoter['promoter_name']) ?>, <?= htmlspecialchars($promoter['promoter_city']) ?></span>
                    </div>
                </div>

                <?php
                if (isset($_SESSION['register_role']) && $_SESSION['register_role'] == '1') {
                    if ($event['status_id'] == 2) {
                        if ($isRegistered) {
                            echo '<div class="alert">✅ Вы зарегистрированы на это мероприятие</div>';
                        } else {
                ?>
                            <form action="/joinEvent" method="POST">
                                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                                <button type="submit" class="btn">📝 Зарегистрироваться на мероприятие</button>
                            </form>
                <?php
                        }
                    } elseif ($event['status_id'] == 3) {
                        echo '<div class="alert alert-warning">⚠️ Мероприятие уже идёт, регистрация закрыта</div>';
                    } elseif ($event['status_id'] == 4) {
                        echo '<div class="alert">🏁 Мероприятие завершено</div>';
                    }
                } elseif (!isset($_SESSION['register_id'])) {
                    echo '<div class="alert alert-warning">🔐 <a href="/login" style="color: #f57c00;">Войдите</a> чтобы зарегистрироваться на мероприятие</div>';
                }
                ?>
            </div>
        </div>

        <?php
        if (isset($_SESSION['register_role']) && $_SESSION['register_role'] == '1' && $event['status_id'] == '4') {
            $userQuery = mysqli_query($db, "SELECT `user_id` FROM `user` WHERE `register_id` = '{$_SESSION['register_id']}'");
            $user = mysqli_fetch_assoc($userQuery);
            $user_id = $user['user_id'];
            
            $participationCheck = mysqli_query($db, "SELECT * FROM `participation` WHERE `user_id` = '$user_id' AND `event_id` = '$event_id' AND `attendance` = '1'");
            $hasParticipated = mysqli_num_rows($participationCheck) > 0;
            
            if ($hasParticipated) {
                $reviewCheck = mysqli_query($db, "SELECT * FROM `review` WHERE `user_id` = '$user_id' AND `event_id` = '$event_id'");
                $hasReviewed = mysqli_num_rows($reviewCheck) > 0;
                
                if (!$hasReviewed) {
        ?>
                    <div class="form-section">
                        <h3>✍️ Оставить отзыв об организаторе</h3>
                        <form action="/addReview" method="POST">
                            <input type="hidden" name="user_id" value="<?= $user_id ?>">
                            <input type="hidden" name="event_id" value="<?= $event_id ?>">
                            
                            <div class="form-group">
                                <label>Оценка (1-5):</label>
                                <select name="review_score" required>
                                    <option value="">Выберите оценку</option>
                                    <option value="5">5 - Отлично</option>
                                    <option value="4">4 - Хорошо</option>
                                    <option value="3">3 - Удовлетворительно</option>
                                    <option value="2">2 - Плохо</option>
                                    <option value="1">1 - Ужасно</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Ваш отзыв:</label>
                                <textarea name="review_text" rows="4" placeholder="Расскажите о своем опыте участия..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn">📨 Отправить отзыв</button>
                        </form>
                    </div>
        <?php
                } else {
                    echo '<div class="form-section"><div class="alert">✅ Вы уже оставили отзыв на это мероприятие. Спасибо!</div></div>';
                }
            }
        }
        
        $reviews = mysqli_query($db, "SELECT r.*, u.user_name, u.user_lastname 
            FROM `review` r 
            JOIN `user` u ON r.user_id = u.user_id 
            WHERE r.event_id = '$event_id' 
            ORDER BY r.review_id DESC");
        
        if (mysqli_num_rows($reviews) > 0) {
        ?>
            <div class="reviews-section">
                <h3>💬 Отзывы участников</h3>
                <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <span class="reviewer-name">👤 <?= htmlspecialchars($review['user_lastname']) ?> <?= htmlspecialchars($review['user_name']) ?></span>
                            <span class="review-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= $review['review_score'] ? '★' : '☆' ?>
                                <?php endfor; ?>
                            </span>
                        </div>
                        <div class="review-text">
                            <?= htmlspecialchars($review['review_text']) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php
        }
        
        if (isset($_SESSION['register_role']) && $_SESSION['register_role'] == '2') {
            $promoterCheck = mysqli_query($db, "SELECT `promoter_id` FROM `promoter` WHERE `register_id` = '{$_SESSION['register_id']}'");
            $currentPromoter = mysqli_fetch_assoc($promoterCheck);
            
            if ($currentPromoter['promoter_id'] == $event['promoter_id'] && ($event['status_id'] == 2 || $event['status_id'] == 3)) {
        ?>
                <div class="form-section">
                    <form action="/finishEvent" method="POST">
                        <input type="hidden" name="event_id" value="<?= $event_id ?>">
                        <button type="submit" class="btn btn-danger">🏁 Завершить мероприятие</button>
                    </form>
                </div>
        <?php
            }
        }
        ?>
        
        <a href="/events" class="back-link">← Назад к списку мероприятий</a>
    </div>
</body>
</html>