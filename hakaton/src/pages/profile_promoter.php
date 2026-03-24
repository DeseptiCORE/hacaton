<?php
// Данные из БД (оставляем логику без изменений)
$promoterQuery = mysqli_query($db, "SELECT p.*, r.register_email, r.register_role 
    FROM `promoter` p 
    JOIN `register` r ON p.register_id = r.register_id 
    WHERE p.register_id = '$register_id'");
$promoter = mysqli_fetch_assoc($promoterQuery);
$promoter_id = $promoter['promoter_id'];

$reviews = mysqli_query($db, "SELECT r.*, u.user_name, u.user_lastname, e.event_name 
    FROM `review` r 
    JOIN `user` u ON r.user_id = u.user_id 
    JOIN `event` e ON r.event_id = e.event_id 
    WHERE e.promoter_id = '$promoter_id' 
    ORDER BY r.review_id DESC");

$avgRating = mysqli_fetch_assoc(mysqli_query($db, "SELECT AVG(r.review_score) as avg, COUNT(*) as count 
    FROM `review` r 
    JOIN `event` e ON r.event_id = e.event_id 
    WHERE e.promoter_id = '$promoter_id'"));

$events = mysqli_query($db, "SELECT * FROM `event` WHERE `promoter_id` = '$promoter_id' ORDER BY `event_date` DESC");
$totalEvents = mysqli_num_rows($events);
$activeEvents = mysqli_num_rows(mysqli_query($db, "SELECT * FROM `event` WHERE `promoter_id` = '$promoter_id' AND `status_id` IN (1,2,3)"));
$completedEvents = mysqli_num_rows(mysqli_query($db, "SELECT * FROM `event` WHERE `promoter_id` = '$promoter_id' AND `status_id` = '4'"));
$totalParticipants = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as cnt FROM `participation` p JOIN `event` e ON p.event_id = e.event_id WHERE e.promoter_id = '$promoter_id'"));
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Профиль организатора</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; line-height: 1.4; background: #f4f7ff; color: #1A1F2E; min-height: 100vh; }
        .header { background-color: #9FBFFF; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 1.8rem; font-weight: 800; background: linear-gradient(90deg, #0022CD, #A100FF); -webkit-background-clip: text; color: transparent; letter-spacing: -0.02em; text-decoration: none; }
        .nav-links { display: flex; align-items: center; gap: 32px; flex-wrap: wrap; }
        .nav-links a { text-decoration: none; font-weight: 500; color: #1A1F2E; transition: 0.2s; font-size: 1rem; }
        .nav-links a:hover { color: #0022CD; }
        .profile-link { background: #F40BA6; padding: 10px 24px; border-radius: 40px; color: white !important; font-weight: 600; transition: 0.2s; opacity: 0.7; }
        .profile-link:hover { opacity: 1; transition: 0.2s; transform: scale(0.96); background: #0022CD; }
        .logout-link { color: #cd001f !important; font-weight: 600; }

        body { background: linear-gradient(90deg,#DFE4FF,#9FBFFF,#DFE4FF); position: relative; overflow-x: hidden; }
       
        .bg-circle { position:fixed; border-radius:50%; border:24px solid rgba(0,0,255,0.1); pointer-events: none; z-index: 0; }
        .circle-1 { width:280px; height:280px; top:-80px; left:-100px;}
        .circle-2 { width:400px; height:400px; top:20%; right:-150px;}

        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; position: relative; z-index: 1; }
        
        .card { 
            background: linear-gradient(180deg, #6A5AE0, #4B3FCF); 
            border-radius:25px; 
            padding:30px; 
            color:white; 
            margin-bottom:20px; 
            box-shadow: 0 10px 30px rgba(75, 63, 207, 0.2);
        }

        .profile-header { display:flex; gap:30px; flex-wrap:wrap; align-items:center; }
        .avatar { 
            width:100px; height:100px; border-radius:50%; background:#8FA6FF; 
            display:flex; align-items:center; justify-content:center; 
            font-size:32px; font-weight: bold; border: 4px solid rgba(255,255,255,0.3);
        }

        .stats-grid { display: flex; gap: 40px; margin-left: auto; }
        .stat-item h3 { font-size: 28px; margin-bottom: 4px; }
        .stat-item p { opacity: 0.8; font-size: 14px; }


        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 14px; opacity: 0.9; }
        input, select { padding: 12px; border-radius: 12px; border: none; background: white; color: #1A1F2E; }
        
        .btn { border:none; padding:12px 24px; border-radius:30px; cursor:pointer; font-weight:600; transition: 0.3s; }
        .save-btn { background:#F40BA6;; color:white; width: fit-content; margin-top: 20px; }
        .save-btn:hover { background: #0022CD; color:white; width: fit-content; margin-top: 20px; transition: 0.2s; transform: scale(0.96); }
        .new-event-btn { background: #F40BA6; color: white; text-decoration: none; display: inline-block; margin-bottom: 20px; }
        .new-event-btn:hover { background: #0022CD; color: white; text-decoration: none; display: inline-block; margin-bottom: 20px; transition: 0.2s; transform: scale(0.96); }

        .table-container { overflow-x: auto; background: rgba(255,255,255,0.1); border-radius: 15px; padding: 10px; }
        table { width: 100%; border-collapse: collapse; color: white; }
        th { text-align: left; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.2); opacity: 0.8; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .event-link { color: #FFD84D; text-decoration: none; font-weight: 600; }

        .review-item { 
            background: rgba(255,255,255,0.1); 
            padding: 20px; 
            border-radius: 15px; 
            margin-bottom: 15px; 
            border-left: 4px solid #FFD84D;
        }
        .stars { color: #FFD84D; margin: 5px 0; }

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
    </style>
</head>
<body>

    <div class="bg-circle circle-1"></div>
    <div class="bg-circle circle-2"></div>

    <header class="header">
        <a href="/" class="logo">EventHub</a>
        <div class="nav-links">
            <a href="/top">Топ участников</a>
            <a href="/events">Мероприятия</a>
            <a href="/organizers">Организаторы</a>
            <a href="/logout" class="logout-link">Выйти</a>
            <a href="/profile" class="profile-link">Личный кабинет</a>
        </div>
    </header>

    <div class="container">
        
        <div class="card profile-header">
            <div class="avatar">
                <?= mb_substr($promoter['promoter_name'], 0, 1, 'UTF-8') ?>
            </div>
            <div>
                <h1 style="margin-bottom: 5px;"><?= $promoter['promoter_name'] ?></h1>
                <p><?= $promoter['promoter_city'] ?> • <?= $promoter['promoter_type'] == 1 ? 'Частное лицо' : 'Организация' ?></p>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <h3><?= $totalEvents ?></h3>
                    <p>Мероприятий</p>
                </div>
                <div class="stat-item">
                    <h3><?= $avgRating['avg'] ? number_format($avgRating['avg'], 1) : '—' ?> ⭐</h3>
                    <p>Рейтинг (<?= $avgRating['count'] ?>)</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px;">Настройки профиля</h3>
            <form action="/updateProfile" method="POST" class="form-grid">
                <div class="form-group">
                    <label>Название</label>
                    <input type="text" name="promoter_name" value="<?= $promoter['promoter_name'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Email (логин)</label>
                    <input type="email" name="register_email" value="<?= $promoter['register_email'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Город</label>
                    <input type="text" name="promoter_city" value="<?= $promoter['promoter_city'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Тип аккаунта</label>
                    <select name="promoter_type" required>
                        <option value="1" <?= $promoter['promoter_type'] == 1 ? 'selected' : '' ?>>Частное лицо</option>
                        <option value="2" <?= $promoter['promoter_type'] == 2 ? 'selected' : '' ?>>Организация</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Новый пароль (оставьте пустым для сохранения текущего)</label>
                    <input type="password" name="new_password">
                </div>
                <button type="submit" class="btn save-btn">Обновить данные</button>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px;">Статистика по мероприятиям</h3>
            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                <p>Завершено: <strong><?= $completedEvents ?></strong></p>
                <p>В процессе: <strong><?= $activeEvents ?></strong></p>
                <p>Всего участников: <strong><?= $totalParticipants['cnt'] ?></strong></p>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Мои мероприятия</h3>
                <a href="/event_new" class="btn new-event-btn">Создать новое</a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Дата мероприятия</th>
                            <th>Статус</th>
                            <th style="text-align: center;">Участники</th>
                            <th style="text-align: right;">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php mysqli_data_seek($events, 0);
                        while ($event = mysqli_fetch_assoc($events)): 
                            $participantsCount = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as cnt FROM `participation` WHERE `event_id` = '{$event['event_id']}'"));
                            $status_text = '';
                            if ($event['status_id'] == 1) $status_text = 'Модерация';
                            elseif ($event['status_id'] == 2) $status_text = 'Сбор заявок';
                            elseif ($event['status_id'] == 3) $status_text = 'Идёт';
                            else $status_text = 'Завершено';
                        ?>
                        <tr>
                            <td><a href="/event?id=<?= $event['event_id'] ?>" class="event-link"><?= $event['event_name'] ?></a></td>
                            <td><?= date('d.m.Y', strtotime($event['event_date'])) ?></td>
                            <td><?= $status_text ?></td>
                            <td style="text-align: center;"><?= $participantsCount['cnt'] ?></td>
                            <td style="text-align: right;">
                                <?php if ($event['status_id'] == 2 || $event['status_id'] == 3): ?>
                                    <form action="/finishEvent" method="POST" style="display:inline;">
                                        <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                        <input type="submit" value="Завершить" class="btn logout-btn" style="font-size: 12px; padding: 5px 12px;">
                                    </form>
                                <?php endif; ?>
                                <?php if ($event['status_id'] == 4): ?>
                                    <a href="/rateEvent?id=<?= $event['event_id'] ?>"><button class="btn save-btn" style="margin: 0; padding: 5px 12px; font-size: 12px;">Итоги</button></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px;">Отзывы участников</h3>
            <?php if (mysqli_num_rows($reviews) == 0): ?>
                <p style="opacity: 0.7;">Пока нет отзывов о ваших мероприятиях.</p>
            <?php else: ?>
                <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                    <div class="review-item">
                        <div style="display: flex; justify-content: space-between;">
                            <strong><?= $review['user_lastname'] ?> <?= $review['user_name'] ?></strong>
                            <span style="font-size: 12px; opacity: 0.7;">на: <?= $review['event_name'] ?></span>
                        </div>
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++) echo $i <= $review['review_score'] ? '★' : '☆'; ?>
                        </div>
                        <p style="margin-top: 10px; font-style: italic;">"<?= htmlspecialchars($review['review_text']) ?>"</p>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

    </div>

    <footer class="footer">
        <div class="footer-logo">EventHub</div>
        <div class="footer-tagline">Молодежная платформа развития потенциала и новых знакомств</div>
        <div class="footer-copyright">© 2026 EventHub. Вдохновляем, развиваем, помогаем расти</div>
    </footer>

</body>
</html>