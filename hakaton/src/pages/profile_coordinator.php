<?php
session_start();
use config\Database;
$db = Database::getConnection();

$register_id = $_SESSION['register_id'] ?? 0;

$coordinatorQuery = mysqli_query($db, "SELECT tc.*, r.register_email, r.register_role 
    FROM `talent_coordinator` tc 
    JOIN `register` r ON tc.register_id = r.register_id 
    WHERE tc.register_id = '$register_id'");
$coordinator = mysqli_fetch_assoc($coordinatorQuery);
$coordinator_id = $coordinator['talent_coordinator_id'];

$topPromoters = mysqli_query($db, "SELECT p.promoter_name, COUNT(e.event_id) as event_count 
    FROM `promoter` p 
    LEFT JOIN `event` e ON p.promoter_id = e.promoter_id 
    GROUP BY p.promoter_id 
    ORDER BY event_count DESC 
    LIMIT 5");

$topUsers = mysqli_query($db, "SELECT u.user_name, u.user_lastname, r.scores 
    FROM `rating` r 
    JOIN `user` u ON r.user_id = u.user_id 
    WHERE r.event_category_id = '4' 
    ORDER BY r.scores DESC 
    LIMIT 10");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой профиль - Кадровый агент | EventHub</title>
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

        .profile-header {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .profile-cover {
            height: 120px;
            background: linear-gradient(135deg, #F40BA6, #6366F1);
        }

        .profile-info {
            padding: 30px;
            text-align: center;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #7C3AED, #6366F1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin: -50px auto 20px;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-name {
            font-size: 28px;
            font-weight: 800;
            color: #333;
            margin-bottom: 5px;
        }

        .profile-role {
            color: #6366F1;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .profile-email {
            color: #888;
            margin-bottom: 5px;
        }

        .profile-org {
            color: #888;
            font-size: 14px;
        }

        .section-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn-submit {
            background: #F40BA6;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-submit:hover {
            background: #0022CD;
            transform: scale(0.98);
        }

        .list-ol {
            padding-left: 20px;
        }

        .list-ol li {
            padding: 10px 0;
            color: #555;
            border-bottom: 1px solid #f0f0f0;
        }

        .list-ol li:last-child {
            border-bottom: none;
        }

        .list-ol li strong {
            color: #333;
        }

        .two-columns {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        @media (max-width: 768px) {
            .two-columns {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .profile-name {
                font-size: 22px;
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
            <a href="/profile" class="profile-link">Личный кабинет</a>
            <a href="/logout" class="logout-link">Выйти</a>
        </div>
    </header>

    <div class="main-container">
        <div class="profile-header">
            <div class="profile-cover"></div>
            <div class="profile-info">
                <div class="profile-avatar">👔</div>
                <h1 class="profile-name"><?= htmlspecialchars($coordinator['talent_coordinator_lastname']) ?> <?= htmlspecialchars($coordinator['talent_coordinator_name']) ?></h1>
                <div class="profile-role">Кадровый агент • <?= $coordinator['talent_coordinator_type'] == 1 ? 'Внутренний' : 'Внешний' ?></div>
                <div class="profile-email">📧 <?= htmlspecialchars($coordinator['register_email'])?></div>
                <div class="profile-org">🏢 <?= htmlspecialchars($coordinator['talent_coordinator_org'])?></div>
            </div>
        </div>

        <div class="section-card">
            <h2 class="section-title">✏️ Редактирование профиля</h2>
            <form action="/updateProfile" method="POST">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="register_email" value="<?= htmlspecialchars($coordinator['register_email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Фамилия:</label>
                    <input type="text" name="talent_coordinator_lastname" value="<?= htmlspecialchars($coordinator['talent_coordinator_lastname']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Имя:</label>
                    <input type="text" name="talent_coordinator_name" value="<?= htmlspecialchars($coordinator['talent_coordinator_name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Отчество:</label>
                    <input type="text" name="talent_coordinator_midname" value="<?= htmlspecialchars($coordinator['talent_coordinator_midname']) ?>">
                </div>
                
                <div class="form-group">
                    <label>Организация:</label>
                    <input type="text" name="talent_coordinator_org" value="<?= htmlspecialchars($coordinator['talent_coordinator_org']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Тип:</label>
                    <select name="talent_coordinator_type" required>
                        <option value="1" <?= $coordinator['talent_coordinator_type'] == 1 ? 'selected' : '' ?>>Внутренний</option>
                        <option value="2" <?= $coordinator['talent_coordinator_type'] == 2 ? 'selected' : '' ?>>Внешний</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Новый пароль (оставьте пустым, если не хотите менять):</label>
                    <input type="password" name="new_password">
                </div>
                
                <button type="submit" class="btn-submit">💾 Сохранить изменения</button>
            </form>
        </div>

        <div class="two-columns">
            <div class="section-card">
                <h2 class="section-title">🏆 Топ организаторов</h2>
                <ol class="list-ol">
                    <?php mysqli_data_seek($topPromoters, 0); ?>
                    <?php while ($promoter = mysqli_fetch_assoc($topPromoters)): ?>
                        <li><strong><?= htmlspecialchars($promoter['promoter_name']) ?></strong> — <?= $promoter['event_count'] ?> мероприятий</li>
                    <?php endwhile; ?>
                </ol>
            </div>

            <div class="section-card">
                <h2 class="section-title">⭐ Топ участников по рейтингу</h2>
                <ol class="list-ol">
                    <?php mysqli_data_seek($topUsers, 0); ?>
                    <?php while ($user = mysqli_fetch_assoc($topUsers)): ?>
                        <li><strong><?= htmlspecialchars($user['user_lastname']) ?> <?= htmlspecialchars($user['user_name']) ?></strong> — <?= $user['scores'] ?> баллов</li>
                    <?php endwhile; ?>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>