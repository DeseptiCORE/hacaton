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
$event = mysqli_fetch_assoc($eventQuery);

$promoterQuery = mysqli_query($db, "SELECT * FROM `promoter` WHERE `promoter_id` = '{$event['promoter_id']}'");
$promoter = mysqli_fetch_assoc($promoterQuery);

$currentPromoter = mysqli_query($db, "SELECT `promoter_id` FROM `promoter` WHERE `register_id` = '{$_SESSION['register_id']}'");
$current = mysqli_fetch_assoc($currentPromoter);

if ($current['promoter_id'] != $event['promoter_id']) {
    echo "<script>alert('Доступ запрещен'); window.location='/';</script>";
    exit();
}

$participants = mysqli_query($db, "SELECT p.*, u.user_name, u.user_lastname, u.user_midname 
    FROM `participation` p 
    JOIN `user` u ON p.user_id = u.user_id 
    WHERE p.event_id = '$event_id'");

// Считаем количество участников для бейджа
$countParticipants = mysqli_num_rows($participants);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Оценка мероприятия</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(90deg, #DFE4FF 0%, #9FBFFF 50%, #DFE4FF 100%);
            display: flex;
            flex-direction: column;
            color: #1A1F2E;
        }

        /* Фоновые круги */
        .bg-circle {
            position: fixed;
            border-radius: 50%;
            border: 24px solid rgba(0,0,255,0.08);
            z-index: 0;
        }
        .circle-1 { width: 280px; height: 280px; top: -80px; left: -100px; }
        .circle-2 { width: 400px; height: 400px; top: 20%; right: -150px; }
        .circle-3 { width: 300px; height: 300px; bottom: 20%; left: -80px; }

        /* Header */
        .header {
            background: #9FBFFF;
            display: flex;
            justify-content: space-between;
            padding: 16px 32px;
            align-items: center;
            position: relative;
            z-index: 10;
        }

        .logo {
            font-weight: 800;
            font-size: 24px;
            background: linear-gradient(90deg,#0022CD,#A100FF);
            -webkit-background-clip: text;
            color: transparent;
            text-decoration: none;
        }

        .nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #1A1F2E;
            font-weight: 500;
        }

        .profile-btn {
            background: #F40BA6;
            padding: 8px 20px;
            border-radius: 30px;
            color: white !important;
        }

        /* Main Content */
        .content {
            flex: 1 0 auto;
            position: relative;
            z-index: 1;
            padding-bottom: 50px;
        }

        .hero {
            text-align: center;
            padding: 40px 20px;
        }

        .hero-text h1 {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(90deg,#0022CD,#A100FF);
            -webkit-background-clip: text;
            color: transparent;
        }

        .event-name {
            color: #4B3FCF;
            font-weight: 700;
        }

        /* Карточка с формой */
        .rating-card {
            max-width: 96%;
            margin: 0 auto;
            background: linear-gradient(180deg, #6A5AE0 0%, #4B3FCF 100%);
            border-radius: 30px;
            padding: 40px;
            color: white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
        }

        /* Таблица */
        .table-wrapper {
            overflow-x: auto;
        }

        .event-table {
            width: 100%;
            border-collapse: collapse;
        }

        .event-table th {
            text-align: left;
            padding: 15px;
            color: #FFD84D;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid rgba(255,255,255,0.1);
        }

        .event-table td {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 35px;
            height: 35px;
            background: #8FA6FF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        /* Стилизация выпадающих списков */
        .styled-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 12px;
            border-radius: 12px;
            outline: none;
            cursor: pointer;
            width: 100%;
            max-width: 200px;
        }

        .styled-select option {
            background: #4B3FCF;
            color: white;
        }

        /* Кнопка сохранения */
        .form-footer {
            margin-top: 40px;
            text-align: center;
        }

        .btn-save {
            background: #FFD84D;
            color: #1A1F2E;
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 216, 77, 0.4);
        }

        /* Footer */
        .footer {
            background: #9FBFFF;
            text-align: center;
            padding: 40px;
            flex-shrink: 0;
            position: relative;
            z-index: 5;
        }

        .footer h2 {
            background: linear-gradient(90deg,#0022CD,#A100FF);
            -webkit-background-clip: text;
            color: transparent;
            margin-bottom: 10px;
        }
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
            <a href="/profile" class="profile-btn">Личный кабинет</a>
        </div>
    </header>

    <main class="content">
        <section class="hero">
            <div class="hero-text">
                <h1>Оценка и результаты</h1>
                <p>Мероприятие: <span class="event-name"><?= htmlspecialchars($event['event_name']) ?></span></p>
            </div>
        </section>

        <form action="/saveResults" method="POST" class="rating-card">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">
            
            <div class="card-header">
                <h2 class="rating-title">Список участников</h2>
                <span class="badge"><?= $countParticipants ?> участников</span>
            </div>

            <div class="table-wrapper">
                <table class="event-table">
                    <thead>
                        <tr>
                            <th>Участник</th>
                            <th>Статус участия</th>
                            <th>Призовое место</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($participant = mysqli_fetch_assoc($participants)): 
                            $initials = mb_substr($user['user_lastname'] ?? $participant['user_lastname'], 0, 1) . mb_substr($user['user_name'] ?? $participant['user_name'], 0, 1);
                        ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="avatar"><?= $initials ?></div>
                                    <span class="name">
                                        <?= htmlspecialchars($participant['user_lastname']) ?> 
                                        <?= htmlspecialchars($participant['user_name']) ?> 
                                        <?= htmlspecialchars($participant['user_midname']) ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <select name="results[<?= $participant['user_id'] ?>][attendance]" class="styled-select" required>
                                    <option value="0" <?= $participant['attendance'] == 0 ? 'selected' : '' ?>>Не участвовал</option>
                                    <option value="1" <?= $participant['attendance'] == 1 ? 'selected' : '' ?>>Участвовал</option>
                                </select>
                            </td>
                            <td>
                                <select name="results[<?= $participant['user_id'] ?>][place]" class="styled-select">
                                    <option value="">Без места</option>
                                    <option value="1" <?= $participant['place'] == 1 ? 'selected' : '' ?>>1 место</option>
                                    <option value="2" <?= $participant['place'] == 2 ? 'selected' : '' ?>>2 место</option>
                                    <option value="3" <?= $participant['place'] == 3 ? 'selected' : '' ?>>3 место</option>
                                </select>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-save">Сохранить результаты</button>
            </div>
        </form>
    </main>

    <footer class="footer">
        <h2>EventHub</h2>
        <p>Молодежная платформа развития потенциала</p>
        <p style="margin-top:15px;font-size:12px;">© 2026 EventHub</p>
    </footer>

</body>
</html>