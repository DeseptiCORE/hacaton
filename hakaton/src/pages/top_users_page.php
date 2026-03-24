<?php
session_start();
use config\Database;
$db = Database::getConnection();

$category_id = isset($_GET['category']) ? $_GET['category'] : '4';

// Получаем список категорий для выпадающего меню
$categoriesQuery = mysqli_query($db, "SELECT * FROM `event_category` ORDER BY event_category_id ASC");
$categories = [];
$currentCategoryName = "Общий рейтинг";

while ($cat = mysqli_fetch_assoc($categoriesQuery)) {
    $categories[] = $cat;
    if ($category_id == $cat['event_category_id']) {
        $currentCategoryName = $cat['event_category_name'];
    }
}

// Запрос топа пользователей
$sql = "SELECT u.user_id, u.user_name, u.user_lastname, u.user_midname, r.scores 
        FROM `rating` r 
        JOIN `user` u ON r.user_id = u.user_id 
        WHERE r.event_category_id = '$category_id' 
        ORDER BY r.scores DESC 
        LIMIT 50";
$topUsers = mysqli_query($db, $sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Топ участников</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        /* Прижимаем футер к низу */
        html, body { height: 100%; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(90deg, #DFE4FF 0%, #9FBFFF 50%, #DFE4FF 100%);
            display: flex;
            flex-direction: column;
        }

        .bg-circle { position: fixed; border-radius: 50%; border: 24px solid rgba(0,0,255,0.15); z-index: 0; }
        .circle-1 { width: 280px; height: 280px; top: -80px; left: -100px; }
        .circle-2 { width: 400px; height: 400px; top: 20%; right: -150px; }
        .circle-3 { width: 300px; height: 300px; bottom: 20%; left: -80px; }

        .header { background: #9FBFFF; display: flex; justify-content: space-between; padding: 16px 32px; align-items: center; position: relative; z-index: 10; flex-shrink: 0; }
        .logo { font-weight: 800; font-size: 24px; background: linear-gradient(90deg,#0022CD,#A100FF); -webkit-background-clip: text; color: transparent; text-decoration: none; }
        .nav a { margin: 0 15px; text-decoration: none; color: #1A1F2E; font-weight: 500; }
        .profile-btn { background: #F40BA6; padding: 8px 20px; border-radius: 30px; color: white !important; }

        /* Контентная область */
        .content { flex: 1 0 auto; position: relative; z-index: 1; }

        .hero { display: flex; align-items: center; justify-content: center; gap: 60px; padding: 40px 20px; }
        .hero-text h1 { font-size: 48px; font-weight: 800; background: linear-gradient(90deg,#0022CD,#A100FF); -webkit-background-clip: text; color: transparent; }
        .hero-text p { margin-top: 10px; font-size: 18px; font-weight: 600; color: #1A1F2E; }

        /* Ширина таблицы 96% */
        .rating-card { 
            max-width: 96%; 
            margin: 0 auto 60px; 
            background: linear-gradient(180deg,#6A5AE0,#4B3FCF); 
            border-radius: 30px; 
            padding: 30px; 
            color: white; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        }

        .rating-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
        .rating-title { font-size: 24px; font-weight: 700; }

        .category-filter { position: relative; }
        .dropdown-btn { background: #3875e8; padding: 8px 18px; border-radius: 20px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .dropdown-menu { position: absolute; right: 0; top: 40px; background: #4B3FCF; border-radius: 15px; display: none; list-style: none; min-width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); overflow: hidden; z-index: 100; }
        .dropdown-menu a { display: block; padding: 12px 18px; color: white; text-decoration: none; font-size: 14px; }
        .dropdown-menu a:hover { background: rgba(255,255,255,0.1); }

        .rating-item { display: flex; justify-content: space-between; align-items: center; padding: 16px 10px; border-bottom: 1px solid rgba(255,255,255,0.1); transition: 0.2s; }
        .rating-item:hover { background: rgba(255,255,255,0.05); border-radius: 10px; }
        .left { display: flex; align-items: center; gap: 15px; }
        .rank { width: 40px; font-weight: 700; color: #FFD84D; font-size: 20px; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; background: #8FA6FF; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .name { font-weight: 500; font-size: 17px; }
        .right { display: flex; align-items: center; gap: 15px; }
        .points { background: #F40BA6; padding: 6px 16px; border-radius: 20px; font-size: 15px; font-weight: 700; min-width: 90px; text-align: center; }
        .btn-pdf { background: #FFD84D; border: none; padding: 6px 15px; border-radius: 15px; cursor: pointer; font-weight: 700; font-size: 12px; }

        .footer { background: #9FBFFF; text-align: center; padding: 40px; flex-shrink: 0; }
        .footer h2 { background: linear-gradient(90deg,#0022CD,#A100FF); -webkit-background-clip: text; color: transparent; margin-bottom: 5px; }
    </style>
</head>
<body>

<div class="bg-circle circle-1"></div>
<div class="bg-circle circle-2"></div>
<div class="bg-circle circle-3"></div>

<header class="header">
    <a href="/" class="logo">EventHub</a>
    <div class="nav">
        <a href="/top_users">Топ участников</a>
        <a href="/events">Мероприятия</a>
        <a href="/organizers">Организаторы</a>
        <?php if (isset($_SESSION['register_id'])): ?>
            <a href="/profile" class="profile-btn">Личный кабинет</a>
        <?php else: ?>
            <a href="/login" class="profile-btn">Войти</a>
        <?php endif; ?>
    </div>
</header>

<main class="content">
    <section class="hero">
        <div class="hero-text">
            <h1>Таблица лидеров</h1>
            <p>Зарабатывай баллы и становись заметным</p>
        </div>
    </section>

    <div class="rating-card">
        <div class="rating-header">
            <div class="rating-title">🏆 <?= htmlspecialchars($currentCategoryName) ?></div>
            <div class="category-filter">
                <div class="dropdown-btn" id="dropdownBtn"><?= htmlspecialchars($currentCategoryName) ?> ▼</div>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="/top_users?category=4">Общий рейтинг</a>
                    <?php foreach ($categories as $cat): ?>
                        <?php if($cat['event_category_id'] != 4): ?>
                            <a href="/top_users?category=<?= $cat['event_category_id'] ?>">
                                <?= htmlspecialchars($cat['event_category_name']) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div id="list">
            <?php 
            $place = 1;
            while ($user = mysqli_fetch_assoc($topUsers)): 
                $initials = mb_substr($user['user_lastname'], 0, 1) . mb_substr($user['user_name'], 0, 1);
            ?>
                <div class="rating-item">
                    <a href="/user_stats?id=<?= $user['user_id'] ?>" class="left" style="text-decoration: none; color: inherit;">
                        <div class="rank"><?= $place ?></div>
                        <div class="avatar"><?= $initials ?></div>
                        <div class="name">
                            <?= htmlspecialchars($user['user_lastname']) ?> 
                            <?= htmlspecialchars($user['user_name']) ?>
                        </div>
                    </a>
                    <div class="right">
                        <div class="points"><?= $user['scores'] ?></div>
                        <?php if (isset($_SESSION['register_role']) && $_SESSION['register_role'] == '3'): ?>
                            <form action="/downloadReport" method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <button type="submit" class="btn-pdf">PDF</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
                $place++;
            endwhile; 
            ?>
        </div>
    </div>
</main>

<footer class="footer">
    <h2>EventHub</h2>
    <p>Молодежная платформа</p>
    <p style="margin-top:15px;font-size:12px;">© 2026 EventHub</p>
</footer>

<script>
    const btn = document.getElementById('dropdownBtn');
    const menu = document.getElementById('dropdownMenu');

    btn.onclick = (e) => {
        e.stopPropagation();
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    };

    document.addEventListener('click', () => {
        menu.style.display = 'none';
    });
</script>

</body>
</html>