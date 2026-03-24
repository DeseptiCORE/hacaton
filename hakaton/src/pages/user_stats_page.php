<?php
session_start();
use config\Database;
$db = Database::getConnection();

$user_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($user_id == 0) {
    echo "<script>alert('Пользователь не найден'); window.location='/';</script>";
    exit();
}

$userQuery = mysqli_query($db, "SELECT u.*, r.register_email, r.register_role 
    FROM `user` u 
    JOIN `register` r ON u.register_id = r.register_id 
    WHERE u.user_id = '$user_id'");
$user = mysqli_fetch_assoc($userQuery);

if (!$user) {
    echo "<script>alert('Пользователь не найден'); window.location='/';</script>";
    exit();
}

$ratingQuery = mysqli_query($db, "SELECT * FROM `rating` WHERE `user_id` = '$user_id' ORDER BY `scores` DESC");
$ratings = [];
while ($rating = mysqli_fetch_assoc($ratingQuery)) {
    $ratings[] = $rating;
}

$generalRating = mysqli_fetch_assoc(mysqli_query($db, "SELECT `scores` FROM `rating` WHERE `user_id` = '$user_id' AND `event_category_id` = '4'"));
$generalScore = $generalRating ? $generalRating['scores'] : 0;

$placeQuery = mysqli_query($db, "SELECT COUNT(*) + 1 as place FROM `rating` WHERE `event_category_id` = '4' AND `scores` > '$generalScore'");
$place = mysqli_fetch_assoc($placeQuery);

$ratingChanges = mysqli_query($db, "SELECT * FROM `rating_change` WHERE `user_id` = '$user_id' AND `event_category_id` = '4' ORDER BY `change_date` ASC");

$participations = mysqli_query($db, "SELECT p.*, e.event_name, e.event_date, ec.event_category_name 
    FROM `participation` p 
    JOIN `event` e ON p.event_id = e.event_id 
    JOIN `event_category` ec ON e.event_category_id = ec.event_category_id 
    WHERE p.user_id = '$user_id' 
    ORDER BY e.event_date DESC");

$categoryCount = [];
mysqli_data_seek($participations, 0);
while ($part = mysqli_fetch_assoc($participations)) {
    $catName = $part['event_category_name'];
    if (isset($categoryCount[$catName])) {
        $categoryCount[$catName]++;
    } else {
        $categoryCount[$catName] = 1;
    }
}
arsort($categoryCount);
mysqli_data_seek($participations, 0);

$reviews = mysqli_query($db, "SELECT r.*, e.event_name, p.promoter_name 
    FROM `review` r 
    JOIN `event` e ON r.event_id = e.event_id 
    JOIN `promoter` p ON e.promoter_id = p.promoter_id 
    WHERE r.user_id = '$user_id' 
    ORDER BY r.review_id DESC");

$initials = mb_substr($user['user_lastname'], 0, 1) . mb_substr($user['user_name'], 0, 1);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Профиль: <?= $user['user_lastname'] ?> <?= $user['user_name'] ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Inter', sans-serif;
            background: linear-gradient(90deg,#DFE4FF,#9FBFFF,#DFE4FF);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .bg-circle { position:fixed; border-radius:50%; border:24px solid rgba(0,0,255,0.15); z-index: 0; }
        .circle-1 { width:280px; height:280px; top:-80px; left:-100px;}
        .circle-2 { width:400px; height:400px; top:20%; right:-150px;}
        .circle-3 { width:300px; height:300px; bottom:20%; left:-80px;}

        .header { background:#9FBFFF; display:flex; justify-content:space-between; padding:16px 32px; align-items: center; position: relative; z-index: 10; }
        .logo { font-weight:800; font-size:24px; background: linear-gradient(90deg,#0022CD,#A100FF); -webkit-background-clip:text; color:transparent; text-decoration: none; }
        .nav-links { display: flex; gap: 20px; align-items: center; }
        .nav-links a { text-decoration: none; color: #1A1F2E; font-weight: 500; }
        .logout-link { background: #F40BA6; padding: 10px 24px; border-radius: 40px; color: white !important; font-weight: 600; transition: 0.2s; }
        .logout-link:hover { background: #d6098f; transform: scale(0.98); }

        .container { max-width:96%; margin:40px auto; flex: 1; position: relative; z-index: 1; }
        .card { background: linear-gradient(180deg,#6A5AE0,#4B3FCF); border-radius:25px; padding:30px; color:white; margin-bottom:20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        .profile-card { display:flex; gap:30px; flex-wrap:wrap; align-items:flex-start; }
        .avatar { width:100px; height:100px; border-radius:50%; background:#8FA6FF; display:flex; align-items:center; justify-content:center; font-size:32px; font-weight: bold; flex-shrink: 0; }
        .profile-info h2 { font-size: 28px; margin-bottom: 5px; }
        .profile-info .role-label { opacity: 0.8; font-size: 14px; margin-bottom: 15px; display: block; }
        
        .stats-summary { margin-left:auto; display:flex; gap:40px; text-align: center; }
        .stat-item h3 { font-size: 36px; color: #FFD84D; }
        .stat-item p { font-size: 14px; opacity: 0.9; }

        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px; }
        .info-item p { font-size: 14px; margin-bottom: 5px; }
        .info-item strong { color: #8FA6FF; margin-right: 5px; }

        .btn { border:none; padding:12px 24px; border-radius:30px; cursor:pointer; font-weight:600; margin-top:20px; transition: 0.3s; }
        .pdf-btn { background: linear-gradient(90deg,#F40BA6,#A100FF); color:white; }
        .pdf-btn:hover { opacity: 0.9; transform: translateY(-2px); }

        .tags { display:flex; gap:10px; flex-wrap: wrap; margin-top: 15px; }
        .tag { background: rgba(255,255,255,0.15); padding:8px 16px; border-radius:20px; font-size: 14px; border: 1px solid rgba(255,255,255,0.1); }
        .tag strong { color: #FFD84D; margin-left: 5px; }

        .table-responsive { overflow-x: auto; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid rgba(255,255,255,0.1); color: #FFD84D; font-size: 14px; }
        td { padding: 15px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 15px; }
        td a { color: #8FA6FF; text-decoration: none; }

        .review-item { background: rgba(255,255,255,0.05); padding: 20px; border-radius: 15px; margin-bottom: 15px; border-left: 4px solid #F40BA6; }
        .stars { color: #FFD84D; margin: 5px 0; }

        .chart-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:20px; }
        .chart-canvas-wrapper { height:350px; position: relative; }

        .footer { background: #9FBFFF; text-align: center; padding: 40px; margin-top: auto; }
        .footer h2 { background: linear-gradient(90deg,#0022CD,#A100FF); -webkit-background-clip: text; color: transparent; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="bg-circle circle-1"></div>
<div class="bg-circle circle-2"></div>
<div class="bg-circle circle-3"></div>

<header class="header">
    <a href="/" class="logo">EventHub</a>
    <div class="nav-links">
        <a href="/top_users">Рейтинг</a>
        <?php if (isset($_SESSION['register_id'])): ?>
            <a href="/profile">Мой профиль</a>
            <a href="/logout" class="logout-link">Выход</a>
        <?php endif; ?>
    </div>
</header>

<div class="container">
    <div class="card profile-card">
        <div class="avatar"><?= $initials ?></div>
        <div class="profile-info">
            <h2><?= $user['user_lastname'] ?> <?= $user['user_name'] ?> <?= $user['user_midname'] ?></h2>
            <span class="role-label"><?= $user['register_role'] == '3' ? 'Администратор' : 'Участник платформы' ?></span>
            
            <div class="info-grid">
                <div class="info-item"><strong>Email:</strong> <?= $user['register_email'] ?></div>
                <div class="info-item"><strong>Город:</strong> <?= $user['user_city'] ?></div>
                <div class="info-item"><strong>Дата рождения:</strong> <?= $user['user_bdate'] ?></div>
            </div>

            <?php if (isset($_SESSION['register_role']) && $_SESSION['register_role'] == '3'): ?>
                <form action="/downloadReport" method="POST">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <button type="submit" class="btn pdf-btn">Скачать PDF отчёт</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="stats-summary">
            <div class="stat-item">
                <h3><?= $generalScore ?></h3>
                <p>Баллы</p>
            </div>
            <div class="stat-item">
                <h3>#<?= $place['place'] ?></h3>
                <p>Место</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Рейтинг по направлениям</h3>
        <div class="tags">
            <?php foreach ($ratings as $rating): 
                if ($rating['event_category_id'] == 4) continue;
                $catData = mysqli_fetch_assoc(mysqli_query($db, "SELECT `event_category_name` FROM `event_category` WHERE `event_category_id` = '{$rating['event_category_id']}'"));
            ?>
                <div class="tag"><?= $catData['event_category_name'] ?>: <strong><?= $rating['scores'] ?></strong></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <div class="chart-header">
            <h3>Динамика роста рейтинга</h3>
        </div>
        <div class="chart-canvas-wrapper">
            <canvas id="ratingChart"></canvas>
        </div>
    </div>

    <div class="card">
        <h3>История участия</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Дата</th>
                        <th>Категория</th>
                        <th>Результат</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($part = mysqli_fetch_assoc($participations)): ?>
                    <tr>
                        <td><a href="/event?id=<?= $part['event_id'] ?>"><?= $part['event_name'] ?></a></td>
                        <td><?= date('d.m.Y', strtotime($part['event_date'])) ?></td>
                        <td><?= $part['event_category_name'] ?></td>
                        <td>
                            <?= $part['attendance'] == 1 ? 'Участвовал' : 'Пропуск' ?>
                            <?= $part['place'] && $part['place'] != '-' ? " (<strong>{$part['place']} место</strong>)" : "" ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3>Отзывы участника</h3>
        <?php if (mysqli_num_rows($reviews) == 0): ?>
            <p style="opacity: 0.6; margin-top: 10px;">Отзывов пока нет</p>
        <?php else: ?>
            <div style="margin-top: 15px;">
                <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                    <div class="review-item">
                        <p><strong>Мероприятие:</strong> <?= $review['event_name'] ?></p>
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++) echo $i <= $review['review_score'] ? '★' : '☆'; ?>
                        </div>
                        <p style="font-style: italic; opacity: 0.9;">"<?= htmlspecialchars($review['review_text']) ?>"</p>
                        <p style="font-size: 12px; margin-top: 10px; opacity: 0.6;">Организатор: <?= $review['promoter_name'] ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <h2>EventHub</h2>
    <p>Молодежная платформа развития потенциала</p>
    <p style="margin-top:15px;font-size:12px;">© 2026 EventHub</p>
</footer>

<script>
    const ctx = document.getElementById('ratingChart').getContext('2d');
    const ratingData = <?php 
        $chartLabels = [];
        $chartScores = [];
        $cumulative = 0;
        mysqli_data_seek($ratingChanges, 0);
        while ($change = mysqli_fetch_assoc($ratingChanges)) {
            $cumulative += $change['scores_change'];
            $chartLabels[] = date('d.m', strtotime($change['change_date']));
            $chartScores[] = $cumulative;
        }
        echo json_encode(['labels' => $chartLabels, 'scores' => $chartScores]);
    ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ratingData.labels,
            datasets: [{
                label: 'Общий рейтинг',
                data: ratingData.scores,
                borderColor: '#FFD84D',
                borderWidth: 3,
                tension: 0.3,
                fill: true,
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) return;
                    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    gradient.addColorStop(0, 'rgba(255, 216, 77, 0.4)');
                    gradient.addColorStop(1, 'rgba(255, 216, 77, 0.0)');
                    return gradient;
                }
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#fff' } },
                x: { grid: { display: false }, ticks: { color: '#fff' } }
            }
        }
    });
</script>

</body>
</html>