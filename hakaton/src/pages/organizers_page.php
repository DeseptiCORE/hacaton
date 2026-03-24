<?php
session_start();
use config\Database;
$db = Database::getConnection();

$register_role = $_SESSION['register_role'] ?? '';
$register_id = $_SESSION['register_id'] ?? null;

$sql = "SELECT p.*, 
        (SELECT COUNT(*) FROM `event` e WHERE e.promoter_id = p.promoter_id) as event_count,
        (SELECT AVG(r.review_score) FROM `review` r 
         JOIN `event` e ON r.event_id = e.event_id 
         WHERE e.promoter_id = p.promoter_id) as avg_rating,
        (SELECT COUNT(*) FROM `review` r 
         JOIN `event` e ON r.event_id = e.event_id 
         WHERE e.promoter_id = p.promoter_id) as review_count
    FROM `promoter` p 
    ORDER BY avg_rating DESC, event_count DESC";

$organizers = mysqli_query($db, $sql);
$total_organizers = mysqli_num_rows($organizers);

$promoter_types = [
    1 => 'Частное лицо',
    2 => 'Организация'
];

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

$formats = [
    'online' => '🖥️ Онлайн',
    'offline' => '🏢 Оффлайн'
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Организаторы | EventHub</title>
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

        .page-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
        }

        .stats-bar {
            background: linear-gradient(135deg, #818CF8, #6366F1);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
            gap: 60px;
            flex-wrap: wrap;
            color: white;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 800;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }

        .organizer-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            transition: box-shadow 0.2s;
        }

        .organizer-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .organizer-header {
            background: linear-gradient(135deg, #F40BA6, #6366F1);
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .organizer-info {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .organizer-avatar {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .organizer-details h2 {
            color: white;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .organizer-details p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .organizer-rating {
            text-align: center;
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 20px;
            border-radius: 12px;
        }

        .rating-stars {
            color: #FFD700;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .rating-text {
            color: white;
            font-size: 12px;
        }

        .organizer-stats {
            display: flex;
            gap: 30px;
            background: #f8f9fa;
            padding: 15px 30px;
            border-bottom: 1px solid #eee;
        }

        .stat-box {
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 800;
            color: #6366F1;
        }

        .stat-label {
            font-size: 12px;
            color: #888;
            margin-top: 3px;
        }

        .events-section {
            padding: 20px 30px;
        }

        .events-section h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .event-card {
            background: #f8f9fa;
            border-radius: 16px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s;
            display: block;
        }

        .event-card:hover {
            transform: translateY(-3px);
        }

        .event-image {
            height: 100px;
            background: linear-gradient(135deg, #7C3AED, #6366F1);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .event-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            color: white;
        }

        .event-content {
            padding: 15px;
        }

        .event-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #333;
        }

        .event-date {
            font-size: 11px;
            color: #888;
            margin-bottom: 8px;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 10px;
            color: #666;
            margin-bottom: 10px;
        }

        .event-meta span {
            background: white;
            padding: 3px 8px;
            border-radius: 12px;
        }

        .event-score {
            font-size: 11px;
            font-weight: 600;
            color: #F40BA6;
        }

        .no-events {
            text-align: center;
            padding: 40px;
            color: #888;
            background: #f8f9fa;
            border-radius: 16px;
        }

        @media (max-width: 768px) {
            .organizer-header {
                flex-direction: column;
                text-align: center;
            }
            
            .organizer-info {
                justify-content: center;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-bar {
                gap: 30px;
            }
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
            
            <?php if ($register_role === '2'): ?>
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
        <div class="page-header">
            <h1>👥 Организаторы</h1>
            <p>Лучшие организаторы мероприятий, которые создают незабываемые события</p>
        </div>


        <?php if ($total_organizers > 0): ?>
            <?php while ($organizer = mysqli_fetch_assoc($organizers)): 
                $avg_rating = round($organizer['avg_rating'], 1);
                $fullStars = floor($avg_rating);
                $hasHalfStar = ($avg_rating - $fullStars) >= 0.5;
                
                $organizerEvents = mysqli_query($db, "SELECT e.*, ec.event_category_name, d.difficulty_name 
                    FROM `event` e 
                    JOIN `event_category` ec ON e.event_category_id = ec.event_category_id 
                    JOIN `difficulty` d ON e.difficulty_id = d.difficulty_id 
                    WHERE e.promoter_id = '{$organizer['promoter_id']}' 
                    ORDER BY e.event_date DESC LIMIT 3");
            ?>
                <div class="organizer-card">
                    <div class="organizer-header">
                        <div class="organizer-info">
                            <div class="organizer-avatar">🏢</div>
                            <div class="organizer-details">
                                <h2><?= htmlspecialchars($organizer['promoter_name']) ?></h2>
                                <p>📍 <?= htmlspecialchars($organizer['promoter_city']) ?> • <?= $promoter_types[$organizer['promoter_type']] ?></p>
                            </div>
                        </div>
                        <div class="organizer-rating">
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $fullStars): ?>
                                        ★
                                    <?php elseif ($hasHalfStar && $i == $fullStars + 1): ?>
                                        ½
                                    <?php else: ?>
                                        ☆
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <div class="rating-text">
                                <?php if ($organizer['review_count'] > 0): ?>
                                    <?= number_format($avg_rating, 1) ?> / 5 (<?= $organizer['review_count'] ?> отзывов)
                                <?php else: ?>
                                    Нет оценок
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="organizer-stats">
                        <div class="stat-box">
                            <div class="stat-number"><?= $organizer['event_count'] ?></div>
                            <div class="stat-label">мероприятий</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?= $organizer['promoter_prize'] != '-' ? $organizer['promoter_prize'] : '0' ?></div>
                            <div class="stat-label">наград</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?= $organizer['review_count'] ?></div>
                            <div class="stat-label">отзывов</div>
                        </div>
                    </div>
                    
                    <div class="events-section">
                        <h3>📅 Последние мероприятия</h3>
                        <?php if (mysqli_num_rows($organizerEvents) > 0): ?>
                            <div class="events-grid">
                                <?php while ($event = mysqli_fetch_assoc($organizerEvents)): ?>
                                    <a href="/event?id=<?= $event['event_id'] ?>" class="event-card">
                                        <div class="event-image">
                                            🎯
                                            <span class="event-status" style="background: <?= $status_colors[$event['status_id']] ?>">
                                                <?= $status_names[$event['status_id']] ?>
                                            </span>
                                        </div>
                                        <div class="event-content">
                                            <h4 class="event-title"><?= htmlspecialchars($event['event_name']) ?></h4>
                                            <div class="event-date">📅 <?= date('d.m.Y H:i', strtotime($event['event_date'])) ?></div>
                                            <div class="event-meta">
                                                <span><?= $formats[$event['event_format']] ?></span>
                                                <span>⚡ <?= htmlspecialchars($event['difficulty_name']) ?></span>
                                                <span>🏷️ <?= htmlspecialchars($event['event_category_name']) ?></span>
                                            </div>
                                            <div class="event-score">🎯 +<?= $event['base_score'] ?> баллов</div>
                                        </div>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                            <?php if ($organizer['event_count'] > 3): ?>
                                <div style="text-align: center; margin-top: 15px;">
                                    <a href="/organizer?id=<?= $organizer['promoter_id'] ?>" style="display: inline-block; background: #6366F1; color: white; padding: 8px 20px; border-radius: 20px; text-decoration: none; font-size: 12px;">
                                        👤 Все мероприятия организатора →
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="no-events">
                                <p>😕 У этого организатора пока нет мероприятий</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-organizers" style="text-align: center; padding: 60px; background: white; border-radius: 20px;">
                <h3>😕 Организаторов пока нет</h3>
                <p>Стань первым организатором и создай своё мероприятие!</p>
                <?php if ($register_role === '2'): ?>
                    <a href="/event_new" style="display: inline-block; margin-top: 20px; background: #6366F1; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none;">Создать мероприятие</a>
                <?php else: ?>
                    <a href="/reg" style="display: inline-block; margin-top: 20px; background: #6366F1; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none;">Зарегистрироваться как организатор</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>