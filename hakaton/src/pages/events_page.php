<?php
session_start();
use config\Database;
$db = Database::getConnection();

$register_role = $_SESSION['register_role'] ?? '';
$register_id = $_SESSION['register_id'] ?? null;

$sql = "SELECT e.*, ec.event_category_name, d.difficulty_name, p.promoter_name 
    FROM `event` e 
    JOIN `event_category` ec ON e.event_category_id = ec.event_category_id 
    JOIN `difficulty` d ON e.difficulty_id = d.difficulty_id 
    JOIN `promoter` p ON e.promoter_id = p.promoter_id 
    ORDER BY e.event_date ASC";

$events = mysqli_query($db, $sql);
$total_events = mysqli_num_rows($events);

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
    <title>Мероприятия | EventHub</title>
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

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .event-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .event-image {
            height: 160px;
            background: linear-gradient(135deg, #7C3AED, #6366F1);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }

        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            color: white;
        }

        .category-badge {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.9);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            color: #6366F1;
        }

        .event-content {
            padding: 20px;
        }

        .event-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }

        .event-description {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 12px;
            color: #888;
        }

        .event-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .organizer {
            font-size: 12px;
            color: #888;
        }

        .score-info {
            font-size: 12px;
            font-weight: 600;
            color: #F40BA6;
        }

        .no-events {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 20px;
            color: #666;
        }

        .no-events h3 {
            margin-bottom: 10px;
            color: #333;
        }

        @media (max-width: 768px) {
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
            <h1>📅 Все мероприятия</h1>
            <p>Найди своё идеальное мероприятие и развивайся вместе с нами</p>
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number"><?= $total_events ?></div>
                <div class="stat-label">Всего мероприятий</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= mysqli_num_rows(mysqli_query($db, "SELECT * FROM `event` WHERE `status_id` = '2'")) ?></div>
                <div class="stat-label">Открыт набор</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= mysqli_num_rows(mysqli_query($db, "SELECT * FROM `event` WHERE `status_id` = '3'")) ?></div>
                <div class="stat-label">Идёт сейчас</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= mysqli_num_rows(mysqli_query($db, "SELECT * FROM `event` WHERE `status_id` = '4'")) ?></div>
                <div class="stat-label">Завершено</div>
            </div>
        </div>

        <?php if ($total_events > 0): ?>
        <div class="events-grid">
            <?php while ($event = mysqli_fetch_assoc($events)): ?>
                <a href="/event?id=<?= $event['event_id'] ?>" class="event-card">
                    <div class="event-image">
                        🎯
                        <span class="status-badge" style="background: <?= $status_colors[$event['status_id']] ?>">
                            <?= $status_names[$event['status_id']] ?>
                        </span>
                        <span class="category-badge">
                            <?= htmlspecialchars($event['event_category_name']) ?>
                        </span>
                    </div>
                    <div class="event-content">
                        <h3 class="event-title"><?= htmlspecialchars($event['event_name']) ?></h3>
                        <p class="event-description"><?= htmlspecialchars(mb_substr($event['event_descr'], 0, 100)) ?>...</p>
                        
                        <div class="event-meta">
                            <span>📅 <?= date('d.m.Y H:i', strtotime($event['event_date'])) ?></span>
                            <span><?= $formats[$event['event_format']] ?></span>
                            <span>⚡ <?= htmlspecialchars($event['difficulty_name']) ?></span>
                        </div>
                        
                        <div class="event-footer">
                            <span class="organizer">👤 <?= htmlspecialchars($event['promoter_name']) ?></span>
                            <span class="score-info">🎯 +<?= $event['base_score'] ?> баллов</span>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="no-events">
            <h3>😕 Мероприятий пока нет</h3>
            <p>Следите за обновлениями, скоро появятся новые мероприятия!</p>
            <a href="/" class="btn-filter" style="display: inline-block; margin-top: 20px; text-decoration: none; background: #6366F1; color: white; padding: 10px 20px; border-radius: 10px;">На главную</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>