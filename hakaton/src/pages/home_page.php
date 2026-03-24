<?php
session_start();
use config\Database;
$db = Database::getConnection();

$register_role = $_SESSION['register_role'] ?? '';
$register_id = $_SESSION['register_id'] ?? null;

$activeParticipants = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(DISTINCT user_id) as count FROM `participation` WHERE `attendance` = 1"))['count'];
$totalEvents = mysqli_num_rows(mysqli_query($db, "SELECT * FROM `event` WHERE `status_id` = 4"));
$totalAwards = mysqli_num_rows(mysqli_query($db, "SELECT * FROM `participation` WHERE `place` IN (1,2,3)"));

$popularEvents = mysqli_query($db, "SELECT e.*, ec.event_category_name 
    FROM `event` e 
    JOIN `event_category` ec ON e.event_category_id = ec.event_category_id 
    WHERE e.status_id = 2 
    ORDER BY e.event_date ASC 
    LIMIT 3");

$topUsers = mysqli_query($db, "SELECT u.user_id, u.user_name, u.user_lastname, r.scores 
    FROM `rating` r 
    JOIN `user` u ON r.user_id = u.user_id 
    WHERE r.event_category_id = 4 
    ORDER BY r.scores DESC 
    LIMIT 5");

$topOrganizers = mysqli_query($db, "SELECT p.promoter_id, p.promoter_name, AVG(r.review_score) as avg_rating, COUNT(r.review_id) as review_count
    FROM `promoter` p 
    LEFT JOIN `event` e ON p.promoter_id = e.promoter_id 
    LEFT JOIN `review` r ON e.event_id = r.event_id 
    GROUP BY p.promoter_id 
    ORDER BY avg_rating DESC 
    LIMIT 5");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Главная</title>
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
            text-align: center;
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

        .footer {
            background-color: #9FBFFF;
            padding: 48px 24px 32px;
            text-align: center;
            margin-top: 0px;
            position: relative;
            z-index: 2;
        }

        .footer-logo {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(90deg, #0022CD, #A100FF);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            display: inline-block;
            margin-bottom: 16px;
        }

        .footer-tagline {
            font-size: 1rem;
            color: #1A1F2E;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .footer-copyright {
            font-size: 0.75rem;
            color: #3A3F5E;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            padding-top: 24px;
            margin-top: 16px;
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

        .hero {
            text-align: center;
            padding: 60px 20px;
        }

        .badge-welcome {
            background: #4F46E5;
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .hero h1 span {
            color: #6366F1;
        }

        .stats-bar {
            background: linear-gradient(135deg, #818CF8, #6366F1);
            max-width: 900px;
            margin: 40px auto;
            border-radius: 30px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            padding: 30px;
            color: white;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }

        .stat-item h3 { 
            font-size: 24px; 
        }
        
        .stat-item p { 
            font-size: 16px; 
            opacity: 0.9;
            font-weight: 600; 
        }

        .section-container {
            max-width: 1100px;
            margin: 0 auto 60px;
            padding: 0 20px;
            text-align: left;
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .event-card {
            background: #7C3AED;
            border-radius: 20px;
            padding: 20px;
            color: white;
            transition: transform 0.2s;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .tag {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 10px;
            border-radius: 10px;
            font-size: 10px;
            display: inline-block;
            margin-bottom: 15px;
        }

        .leaderboard {
            background: #6366F1;
            border-radius: 24px;
            padding: 30px;
            color: white;
        }

        .leader-row {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .leader-row:last-child { 
            border: none; 
        }

        .rank { 
            width: 40px; 
            font-weight: bold; 
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .name { 
            flex-grow: 1; 
        }

        .btn-more {
            display: block;
            width: fit-content;
            margin: 20px auto 0;
            background: #F472B6;
            color: white;
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-size: 14px;
            transition: 0.2s;
        }

        .btn-more:hover {
            background: #F40BA6;
            transform: scale(1.05);
        }

        .event-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .event-date {
            font-size: 12px;
            margin-top: 10px;
            opacity: 0.9;
        }

        .rating-stars {
            color: #FFD700;
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

    <main>
        <section class="hero">
            <div class="badge-welcome">★ Добро пожаловать в EventHub</div>
            <h1>Развивайся, участвуй,<br>попади в <span>кадровый резерв</span></h1>
            <p>Участвуй в мероприятиях, получай баллы, вдохновляй и становись лидером</p>

            <div class="stats-bar">
                <div class="stat-item">
                    <h3><?= number_format($activeParticipants) ?></h3>
                    <p>Активных участников</p>
                </div>
                <div class="stat-item">
                    <h3><?= $totalEvents ?></h3>
                    <p>Мероприятий проведено</p>
                </div>
                <div class="stat-item">
                    <h3><?= $totalAwards ?></h3>
                    <p>Призов выдано</p>
                </div>
            </div>
        </section>

        <section class="section-container">
            <h2 class="section-title">Популярные мероприятия</h2>
            <div class="grid-3">
                <?php if (mysqli_num_rows($popularEvents) > 0): ?>
                    <?php while ($event = mysqli_fetch_assoc($popularEvents)): ?>
                        <a href="/event?id=<?= $event['event_id'] ?>" class="event-link">
                            <div class="event-card">
                                <span class="tag"><?= htmlspecialchars($event['event_category_name']) ?></span>
                                <h3><?= htmlspecialchars($event['event_name']) ?></h3>
                                <p style="font-size: 12px; margin: 10px 0; opacity: 0.8;">
                                    <?= mb_substr(htmlspecialchars($event['event_descr']), 0, 80) ?>...
                                </p>
                                <div style="display: flex; gap: 10px; margin-top: 20px;">
                                    <span class="tag"><?= date('d.m.Y', strtotime($event['event_date'])) ?></span>
                                    <span class="tag"><?= $event['event_format'] == 'online' ? 'Онлайн' : 'Оффлайн' ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <div class="event-card">
                            <span class="tag">Скоро</span>
                            <h3>Новые мероприятия</h3>
                            <p style="font-size: 12px; margin: 10px 0; opacity: 0.8;">Следите за обновлениями</p>
                        </div>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="section-container">
            <div class="leaderboard">
                <h2 class="section-title">Топ участников</h2>
                <?php 
                $place = 1;
                if (mysqli_num_rows($topUsers) > 0):
                    while ($user = mysqli_fetch_assoc($topUsers)): 
                        $initials = mb_substr($user['user_name'], 0, 1) . mb_substr($user['user_lastname'], 0, 1);
                ?>
                    <div class="leader-row">
                        <span class="rank"><?= $place++ ?></span>
                        <div class="avatar-circle"><?= $initials ?></div>
                        <span class="name"><?= htmlspecialchars($user['user_lastname']) ?> <?= htmlspecialchars($user['user_name']) ?></span>
                        <span class="points"><?= $user['scores'] ?> баллов</span>
                    </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <div class="leader-row">
                        <span class="rank">1</span>
                        <div class="avatar-circle">?</div>
                        <span class="name">Пока нет участников</span>
                        <span class="points">0 баллов</span>
                    </div>
                <?php endif; ?>
                <a href="/top_users" class="btn-more">Посмотреть весь топ →</a>
            </div>
        </section>

        <section class="section-container">
            <div class="leaderboard" style="background: #4F46E5;">
                <h2 class="section-title">Популярные организаторы</h2>
                <?php 
                $orgPlace = 1;
                if (mysqli_num_rows($topOrganizers) > 0):
                    while ($organizer = mysqli_fetch_assoc($topOrganizers)):
                        $avgRating = round($organizer['avg_rating'], 1);
                        $fullStars = floor($avgRating);
                ?>
                    <div class="leader-row">
                        <span class="rank"><?= $orgPlace++ ?></span>
                        <div class="avatar-circle"><?= mb_substr($organizer['promoter_name'], 0, 2) ?></div>
                        <span class="name"><?= htmlspecialchars($organizer['promoter_name']) ?></span>
                        <span class="rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?= $i <= $fullStars ? '★' : '☆' ?>
                            <?php endfor; ?>
                            <?= $organizer['review_count'] > 0 ? " ({$organizer['review_count']})" : '' ?>
                        </span>
                    </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <div class="leader-row">
                        <span class="rank">1</span>
                        <div class="avatar-circle">?</div>
                        <span class="name">Пока нет организаторов</span>
                        <span class="rating">☆☆☆☆☆</span>
                    </div>
                <?php endif; ?>
                <a href="/organizers" class="btn-more">Посмотреть всех организаторов →</a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-logo">EventHub</div>
        <div class="footer-tagline">Молодежная платформа развития потенциала и новых знакомств</div>
        <div class="footer-copyright">© 2026 EventHub. Вдохновляем, развиваем, помогаем расти</div>
    </footer>
</body>
</html>