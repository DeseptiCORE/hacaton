<?php
$userQuery = mysqli_query($db, "SELECT u.*, r.register_email, r.register_role 
    FROM `user` u 
    JOIN `register` r ON u.register_id = r.register_id 
    WHERE u.register_id = '$register_id'");
$user = mysqli_fetch_assoc($userQuery);
$user_id = $user['user_id'];

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


$initials = mb_substr($user['user_name'], 0, 1) . mb_substr($user['user_lastname'], 0, 1);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Профиль участника</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Inter', sans-serif;
            background: linear-gradient(90deg,#DFE4FF,#9FBFFF,#DFE4FF);
            min-height: 100vh;
        }
        .bg-circle {
            position:fixed;
            border-radius:50%;
            border:24px solid rgba(0,0,255,0.15);
            z-index: -1;
        }
        .circle-1 { width:280px; height:280px; top:-80px; left:-100px;}
        .circle-2 { width:400px; height:400px; top:20%; right:-150px;}
        .circle-3 { width:300px; height:300px; bottom:20%; left:-80px;}

        .header {
            background:#9FBFFF;
            display:flex;
            justify-content:space-between;
            align-items: center;
            padding:16px 32px;
        }
        .logo {
            font-weight:800;
            font-size:24px;
            background: linear-gradient(90deg,#0022CD,#A100FF);
            -webkit-background-clip:text;
            color:transparent;
            text-decoration: none;
        }
        .nav a {
            margin:0 15px;
            text-decoration:none;
            color:#1A1F2E;
            font-weight: 500;
        }
        .profile-link {
            background:#F40BA6;
            padding:8px 20px;
            border-radius:30px;
            color:white !important;
        }
        .container {
            max-width:96%;
            margin:40px auto;
        }
        .card {
            background: linear-gradient(180deg,#6A5AE0,#4B3FCF);
            border-radius:25px;
            padding:25px;
            color:white;
            margin-bottom:20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .profile-card {
            display:flex;
            gap:20px;
            flex-wrap:wrap;
            align-items:center;
        }
        .avatar {
            width:80px;
            height:80px;
            border-radius:50%;
            background:#8FA6FF;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:26px;
            font-weight: bold;
        }
        .stats {
            margin-left:auto;
            display:flex;
            gap:30px;
            text-align: center;
        }
        .btn {
            border:none;
            padding:10px 20px;
            border-radius:20px;
            cursor:pointer;
            font-weight:600;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }
        .edit-btn { background: linear-gradient(90deg,#F40BA6,#A100FF); color:white; }
        .save-btn { background:#FFD84D; color:#1A1F2E; border: none; width: fit-content; }
        .logout-btn { background:rgba(255,255,255,0.2); color:white; margin-left: 10px;}
        
        .form-grid {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:15px;
            margin-top:15px;
        }
        .form-grid div { display: flex; flex-direction: column; gap: 5px; }
        .form-grid input {
            padding:12px;
            border-radius:10px;
            border:none;
            background: rgba(255,255,255,0.9);
        }
        .tags { display:flex; gap:10px; flex-wrap:wrap; margin-top: 15px; }
        .tag { background:#3875e8; padding:6px 14px; border-radius:20px; font-size: 14px; }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .period-selector {
            display: flex;
            gap: 10px;
            background: rgba(255,255,255,0.15);
            padding: 5px;
            border-radius: 40px;
        }
        .period-btn {
            background: transparent;
            border: none;
            color: white;
            padding: 6px 16px;
            border-radius: 30px;
            cursor: pointer;
        }
        .period-btn.active { background: #FFD84D; color: #1A1F2E; }
        
        .event-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .event-table th { text-align: left; padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.2); color: #FFD84D; }
        .event-table td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .event-table a { color: #8FA6FF; text-decoration: none; }

        .footer { background: #9FBFFF; text-align: center; padding: 40px; }
        .footer h2 { background: linear-gradient(90deg,#0022CD,#A100FF); -webkit-background-clip: text; color: transparent; margin-bottom: 10px;}
        .chart-canvas-wrapper { width:100%; height:350px; position: relative; }
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
        <a href="#" class="profile-link">Личный кабинет</a>
    </div>
</header>

<div class="container">
    <div class="card profile-card">
        <div class="avatar"><?= $initials ?></div>
        <div>
            <h2><?= $user['user_name'] . ' ' . $user['user_lastname'] ?></h2>
            <p><?= $user['user_city'] ?></p>
            <div style="margin-top:15px;">
                <button class="btn edit-btn" onclick="toggleEdit()">Редактировать</button>
                <a href="/logout" class="btn logout-btn">Выйти</a>
            </div>
        </div>
        <div class="stats">
            <div>
                <h3 style="font-size: 28px;"><?= $generalScore ?></h3>
                <p>Баллы</p>
            </div>
            <div>
                <h3 style="font-size: 28px;">#<?= $place['place'] ?></h3>
                <p>Место</p>
            </div>
        </div>
    </div>

    <div class="card" id="editForm" style="display:none;">
        <h3>Настройки профиля</h3>
        <form action="/updateProfile" method="POST" class="form-grid">
            <div>
                <label>Email</label>
                <input type="email" name="register_email" value="<?= $user['register_email'] ?>" required>
            </div>
            <div>
                <label>Фамилия</label>
                <input type="text" name="user_lastname" value="<?= $user['user_lastname'] ?>" required>
            </div>
            <div>
                <label>Имя</label>
                <input type="text" name="user_name" value="<?= $user['user_name'] ?>" required>
            </div>
            <div>
                <label>Отчество</label>
                <input type="text" name="user_midname" value="<?= $user['user_midname'] ?>">
            </div>
            <div>
                <label>Дата рождения</label>
                <input type="date" name="user_bdate" value="<?= $user['user_bdate'] ?>" required>
            </div>
            <div>
                <label>Город</label>
                <input type="text" name="user_city" value="<?= $user['user_city'] ?>" required>
            </div>
            <div>
                <label>Новый пароль</label>
                <input type="password" name="new_password" placeholder="Оставьте пустым">
            </div>
            <div style="grid-column: span 2; align-items: flex-start;">
                <input type="submit" class="btn save-btn" value="Сохранить изменения">
            </div>
        </form>
    </div>

    <div class="card">
        <h3>🏆 Рейтинг по направлениям</h3>
        <div class="tags">
            <?php foreach ($ratings as $rating): 
                if ($rating['event_category_id'] == 4) continue;
                $catData = mysqli_fetch_assoc(mysqli_query($db, "SELECT `event_category_name` FROM `event_category` WHERE `event_category_id` = '{$rating['event_category_id']}'"));
            ?>
                <div class="tag"><?= $catData['event_category_name'] ?>: <?= $rating['scores'] ?></div>
            <?php endforeach; ?>
        </div>
        
        <h3 style="margin-top:25px;">🔥 Популярные у вас</h3>
        <div class="tags">
            <?php foreach ($categoryCount as $name => $count): ?>
                <div class="tag" style="background: rgba(255,255,255,0.1); border: 1px solid white;">
                    <?= $name ?> (<?= $count ?>)
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <div class="chart-header">
            <h3>Динамика рейтинга</h3>
            <div class="period-selector">
                <button class="period-btn" data-period="week">Неделя</button>
                <button class="period-btn active" data-period="month">Месяц</button>
                <button class="period-btn" data-period="year">Год</button>
            </div>
        </div>
        <div class="chart-canvas-wrapper">
            <canvas id="activityChart"></canvas>
        </div>
    </div>

    <div class="card">
        <h3>Мои мероприятия</h3>
        <div style="overflow-x: auto;">
            <table class="event-table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Дата</th>
                        <th>Категория</th>
                        <th>Статус</th>
                        <th>Место</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($part = mysqli_fetch_assoc($participations)): ?>
                    <tr>
                        <td><a href="/event?id=<?= $part['event_id'] ?>"><?= $part['event_name'] ?></a></td>
                        <td><?= date('d.m.Y', strtotime($part['event_date'])) ?></td>
                        <td><?= $part['event_category_name'] ?></td>
                        <td><?= $part['attendance'] == 1 ? '✅ Участвовал' : '❌ Пропустил' ?></td>
                        <td><?= $part['place'] != '-' && $part['place'] != '' ? $part['place'] . ' место' : '-' ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="footer">
    <h2>EventHub</h2>
    <p>Молодежная платформа развития потенциала и новых знакомств</p>
    <p style="margin-top:15px;font-size:12px;">© 2026 EventHub</p>
</footer>

<script>
// Данные из PHP для графика
const dbRatingChanges = <?php 
    $data = [];
    mysqli_data_seek($ratingChanges, 0);
    while ($change = mysqli_fetch_assoc($ratingChanges)) {
        $data[] = ['date' => $change['change_date'], 'score' => $change['scores_change']];
    }
    echo json_encode($data);
?>;

let currentChart = null;
const ctx = document.getElementById('activityChart').getContext('2d');

// Функция обработки данных для графика (накопительный итог)
function processData() {
    let cumulative = 0;
    const labels = [];
    const scores = [];
    dbRatingChanges.forEach(change => {
        cumulative += parseFloat(change.score);
        labels.push(new Date(change.date).toLocaleDateString());
        scores.push(cumulative);
    });
    return { labels, scores };
}

function updateChart(period) {
    const { labels, scores } = processData();
    
    if (currentChart) { currentChart.destroy(); }
    
    currentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Общий рейтинг',
                data: scores,
                borderColor: '#FFD84D',
                backgroundColor: (context) => {
                    const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(255, 216, 77, 0.4)');
                    gradient.addColorStop(1, 'rgba(255, 216, 77, 0)');
                    return gradient;
                },
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 5,
                pointBackgroundColor: '#FFD84D'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#fff' } }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    ticks: { color: '#fff' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#fff' }
                }
            }
        }
    });
}

function toggleEdit() {
    const form = document.getElementById('editForm');
    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
}

// Переключатели периодов (функционал из стилей)
document.querySelectorAll('.period-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        updateChart(this.dataset.period);
    });
});

document.addEventListener('DOMContentLoaded', () => updateChart('month'));
</script>

</body>
</html>