<?php
session_start();
use config\Database;
$db = Database::getConnection();
$difficulties = mysqli_fetch_all(mysqli_query($db, "SELECT * FROM `difficulty`"));
$category = mysqli_fetch_all(mysqli_query($db, "SELECT * FROM `event_category`"));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Создание мероприятия</title>
</head>
<body>
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
    
    .bg-circle {
        position: fixed;
        border-radius: 50%;
        border: 26px solid rgba(21, 25, 255, 0.2);
        background: transparent;
        pointer-events: none;
        z-index: 1;
    }

    .circle-1 { width: 280px; height: 280px; top: -80px; left: -100px; }
    .circle-2 { width: 420px; height: 420px; top: 15%; right: -180px; }
    .circle-3 { width: 350px; height: 350px; bottom: 25%; left: -5px; }
    .circle-4 { width: 380px; height: 380px; bottom: -100px; right: -150px; }

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
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            letter-spacing: -0.02em;
            text-decoration: none;
        }
      
        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
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
            opacity: 0.7;
        }
        .profile-link:hover {
            background: #0022CD;
            padding: 10px 24px;
            border-radius: 40px;
            color: white !important;
            font-weight: 600;
            transition: 0.2s;
            opacity: 0.7;
            transform: scale(0.96);
        }
    
    .form-event {
        max-width: 780px;
        width: 100%;
        margin: 40px auto;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(2px);
        border-radius: 48px;
        padding: 32px 40px 48px;
        box-shadow: 0 20px 35px -12px rgba(0, 34, 205, 0.25);
        text-align: left;
        transition: all 0.2s;
        position: relative;
        z-index: 10;
        border: 1px solid rgba(255, 255, 255, 0.5);
        overflow-x: hidden;
    }

    .gradient-text {
        background: linear-gradient(90deg, #0022CD, #A100FF);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        font-size: 2.5rem;
        font-weight: 800;
        display: inline-block;
        margin-bottom: 28px;
        letter-spacing: -0.01em;
        border-left: 5px solid #0022CD;
        padding-left: 20px;
    }
    
    .form-section {
        background: #f8f9ff;
        border-radius: 28px;
        padding: 24px;
        margin-bottom: 28px;
        border: 1px solid #e2e8ff;
        overflow-x: auto;
    }
    
    .form-section h3 {
        font-size: 1.25rem;
        font-weight: 700;
        background: linear-gradient(120deg, #1f2b4e, #3a2b8c);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid rgba(0, 34, 205, 0.2);
        padding-bottom: 12px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 16px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        font-weight: 600;
        color: #1A1F2E;
        display: block;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    
    .required {
        color: #F40BA6;
        margin-left: 4px;
    }
    
    .form-group input, 
    .form-group select, 
    .form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border-radius: 32px;
        border: 1.5px solid #cfdfef;
        background: white;
        font-family: inherit;
        font-size: 0.95rem;
        transition: 0.2s;
        outline: none;
    }
    
    .form-group input:hover, 
    .form-group select:hover, 
    .form-group textarea:hover {
        border-color: #5B4BD6;
    }

    .form-group input:focus, 
    .form-group select:focus, 
    .form-group textarea:focus {
        border-color: #5B4BD6;
    }
    
    .form-group textarea {
        border-radius: 24px;
        resize: vertical;
        min-height: 100px;
    }
    
    .cover-upload {
        margin-bottom: 20px;
    }
    
    .cover-preview {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #e0e7ff, #f8f9ff);
        border-radius: 24px;
        border: 2px dashed #5B4BD6;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    
    .cover-preview:hover {
        border-color: #0022CD;
        background: linear-gradient(135deg, #d0dbff, #f0f2ff);
    }
    
    .cover-preview.has-image {
        border: 2px solid #5B4BD6;
        background: none;
    }
    
    .cover-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-text {
        font-size: 0.9rem;
        color: #5B4BD6;
        font-weight: 500;
    }
    
    .upload-hint {
        font-size: 0.75rem;
        color: #666;
        margin-top: 8px;
    }
    
    .remove-cover {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        cursor: pointer;
        font-size: 18px;
        display: none;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        z-index: 10;
    }
    
    .remove-cover:hover {
        background: #F40BA6;
        transform: scale(1.1);
    }
    
    .cover-preview.has-image .remove-cover {
        display: flex;
    }
    
    .file-input {
        display: none;
    }
    
    .checkbox-group {
        margin-bottom: 24px;
        padding: 16px;
        background: white;
        border-radius: 20px;
        border: 1.5px solid #e2e8ff;
        transition: all 0.2s;
    }
    
    .checkbox-group:hover {
        border-color: #5B4BD6;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        cursor: pointer;
        gap: 12px;
        font-weight: 600;
        color: #1A1F2E;
        margin-bottom: 0;
    }
    
    .checkbox-label input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #0022CD;
        flex-shrink: 0;
    }
    
    .checkbox-label span {
        font-size: 1rem;
    }
    
    .checkbox-hint {
        margin-top: 8px;
        margin-left: 32px;
        font-size: 0.8rem;
        color: #666;
    }
    
    .prize-places-container {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
    }
    
    .score-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(140px, 1fr));
        gap: 16px;
        margin-top: 16px;
        transition: all 0.3s ease;
    }
    
    .score-grid.hidden {
        display: none;
    }
    
    .score-card {
        background: white;
        border-radius: 20px;
        padding: 16px;
        text-align: center;
        border: 1.5px solid #e2e8ff;
        transition: all 0.2s;
        animation: fadeIn 0.3s ease;
        min-width: 0;
    }
    

    
    .score-card:hover {
        border-color: #5B4BD6
    }
    
    
    .score-card label {
        font-size: 1rem;
        margin-bottom: 8px;
        color: #1A1F2E;
        display: block;
        font-weight: 600;
    }
    
    .score-card input {
        width: 100%;
        text-align: center;
        font-size: 1.1rem;
        font-weight: 600;
        padding: 8px 12px;
    }
    

    
    .submit-btn {
        background: linear-gradient(90deg, #0022CD, #6b3eff);
        border: none;
        width: 100%;
        padding: 16px;
        border-radius: 60px;
        font-weight: 700;
        font-size: 1.1rem;
        color: white;
        margin-top: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 8px 18px rgba(0, 34, 205, 0.25);
        font-family: inherit;
    }

    .cancel-btn {
        background: linear-gradient(90deg, #cd001f, #ff3e3e);
        border: none;
        width: 100%;
        padding: 16px;
        border-radius: 60px;
        font-weight: 700;
        font-size: 1.1rem;
        color: white;
        margin-top: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 8px 18px rgba(0, 34, 205, 0.25);
        font-family: inherit;
    }

    .submit-btn:hover {
        background: linear-gradient(90deg, #0022CD, #6b3eff );
        border: none;
        width: 100%;
        padding: 16px;
        border-radius: 60px;
        font-weight: 700;
        font-size: 1.1rem;
        color: white;
        margin-top: 16px;
        cursor: pointer;
        box-shadow: 0 8px 18px rgba(0, 34, 205, 0.25);
        font-family: inherit;
        transition: 0.2s;
        transform: scale(0.96);

    }

    .cancel-btn:hover {
        background: linear-gradient(90deg, #cd001f, #ff3e3e);
        border: none;
        width: 100%;
        padding: 16px;
        border-radius: 60px;
        font-weight: 700;
        font-size: 1.1rem;
        color: white;
        margin-top: 16px;
        cursor: pointer;
        box-shadow: 0 8px 18px rgba(0, 34, 205, 0.25);
        font-family: inherit;
        transition: 0.2s;
        transform: scale(0.96);
        
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
    
    </style>
    
    <div class="bg-circle circle-1"></div>
    <div class="bg-circle circle-2"></div>
    <div class="bg-circle circle-3"></div>
    <div class="bg-circle circle-4"></div>

    <header class="header">
        <a href="/" class="logo">EventHub</a>
        <div class="nav-links">
            <a href="/top_users">Топ участников</a>
            <a href="/events">Мероприятия</a>
            <a href="/organizers">Организаторы</a>
            <a href="/profile" class="profile-link">Личный кабинет</a>
        </div>
    </header>
    
    <div class="form-event">
        <form action="addEvent" method="POST" id="eventForm" enctype="multipart/form-data">
            <span class="gradient-text">Создание мероприятия</span>
            
            <div class="form-section">
                <h3>Обложка мероприятия</h3>
                
                <div class="cover-upload">
                    <div class="cover-preview" id="coverPreview" onclick="document.getElementById('coverInput').click()">
                        <div class="upload-text">Нажмите для загрузки обложки</div>
                        <div class="upload-hint">Рекомендуемый размер: 1200x630px. Поддерживаются JPG, PNG, GIF</div>
                        <button type="button" class="remove-cover" id="removeCoverBtn" onclick="event.stopPropagation(); removeCover()">×</button>
                    </div>
                    <input type="file" name="event_cover" id="coverInput" class="file-input" accept="image/jpeg,image/png,image/gif,image/webp">
                </div>
            </div>
            <div class="form-section">
                <h3>Основная информация мероприятия</h3>
                
                <div class="form-group">
                    <label>Название мероприятия:<span class="required">*</span></label>
                    <input type="text" name="event_name" placeholder="Введите название мероприятия" required>
                </div>
                
                <div class="form-group">
                    <label>Описание:<span class="required">*</span></label>
                    <textarea name="event_descr" rows="4" placeholder="Расскажите подробнее о мероприятии..." required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Категория:<span class="required">*</span></label>
                        <select name="event_category_id" required>
                            <option value="">Выберите категорию</option>
                            <?php
                            foreach($category as $cat){
                                ?>
                                <option value="<?=$cat[0]?>"><?=$cat[1]?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Сложность:<span class="required">*</span></label>
                        <select name="difficulty_id" required>
                            <option value="">Выберите сложность:<span class="required">*</span></option>
                            <?php
                            $count = 0;
                            foreach($difficulties as $diff){
                                $count++;
                                if($count == 4){
                                    continue; 
                                }
                                ?>
                                <option value="<?=$diff[0]?>"><?=$diff[1]?> (коэф. <?=$diff[2]?>)</option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Дата проведения:<span class="required">*</span></label>
                        <input type="datetime-local" name="event_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Формат:<span class="required">*</span></label>
                        <select name="event_format" required>
                            <option value="">Выберите формат</option>
                            <option value="online">Онлайн</option>
                            <option value="offline">Оффлайн</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Система начисления баллов</h3>
                
                <div class="form-group">
                    <label>Базовый балл (за участие):<span class="required">*</span></label>
                    <input type="number" name="base_score" value="0" step="1" required>
                    <small style="display: block; margin-top: 5px; color: #666;">Баллы, которые получает каждый участник мероприятия</small>
                </div>
                
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="has_prize_places" id="hasPrizePlaces" value="1">
                        <span>Призовые места</span>
                    </label>
                    <div class="checkbox-hint">Отметьте, если в мероприятии будут определены победители</div>
                </div>
                <div id="prizePlacesBlock" style="display: none;">
                    <div class="prize-places-container">
                        <div class="score-grid">
                            <div class="score-card">
                                <label>1 место</label>
                                <input type="number" name="firstp_score" value="0" step="1" id="firstPlaceScore" placeholder="Баллы">
                            </div>
                            
                            <div class="score-card">
                                <label>2 место</label>
                                <input type="number" name="secondp_score" value="0" step="1" id="secondPlaceScore" placeholder="Баллы">
                            </div>
                            
                            <div class="score-card">
                                <label>3 место</label>
                                <input type="number" name="thirdp_score" value="0" step="1" id="thirdPlaceScore" placeholder="Баллы">
                            </div>
                        </div>
                    </div>
                    <small style="display: block; margin-top: 12px; color: #666; text-align: center;">Укажите количество баллов для каждого призового места</small>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Дополнительно</h3>
                
                <div class="form-group">
                    <label>Бонусы от организатора:</label>
                    <textarea name="event_bonus" rows="4" placeholder="Например: встречи, подарки, сертификаты, мерч..."></textarea>
                    <small style="display: block; margin-top: 5px; color: #666;">Информация о специальных бонусах для участников</small>
                </div>
            </div>
            
            <input type="submit" value="Создать мероприятие" class="submit-btn">
            <input type="button" value="Отмена" class="cancel-btn" onclick="window.history.back()">
        </form>
    </div>
    
    <footer class="footer">
        <div class="footer-logo">EventHub</div>
        <div class="footer-tagline">Молодежная платформа развития потенциала и новых знакомств</div>
        <div class="footer-copyright">© 2026 EventHub. Вдохновляем, развиваем, помогаем расти</div>
    </footer>
    
    <script>
        function removeCover() {
            const coverInput = document.getElementById('coverInput');
            const coverPreview = document.getElementById('coverPreview');
            
            coverInput.value = '';
            const img = coverPreview.querySelector('img');
            if (img) img.remove();
            coverPreview.classList.remove('has-image');
            
            coverPreview.innerHTML = `
                <div class="upload-text">Нажмите для загрузки обложки</div>
                <div class="upload-hint">Рекомендуемый размер: 1200x630px. Поддерживаются JPG, PNG, GIF</div>
                <button type="button" class="remove-cover" onclick="event.stopPropagation(); removeCover()">×</button>
            `;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.querySelector('input[name="event_date"]');
            if (dateInput) {
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                const minDateTime = now.toISOString().slice(0, 16);
                dateInput.min = minDateTime;
            }
            
            const hasPrizeCheckbox = document.getElementById('hasPrizePlaces');
            const prizePlacesBlock = document.getElementById('prizePlacesBlock');
            const firstPlaceScore = document.getElementById('firstPlaceScore');
            const secondPlaceScore = document.getElementById('secondPlaceScore');
            const thirdPlaceScore = document.getElementById('thirdPlaceScore');
            
            function togglePrizePlaces() {
                if (hasPrizeCheckbox.checked) {
                    prizePlacesBlock.style.display = 'block';
                    prizePlacesBlock.style.animation = 'fadeIn 0.3s ease';
                    if (firstPlaceScore) firstPlaceScore.required = true;
                    if (secondPlaceScore) secondPlaceScore.required = true;
                    if (thirdPlaceScore) thirdPlaceScore.required = true;
                } else {
                    prizePlacesBlock.style.display = 'none';
                    if (firstPlaceScore) firstPlaceScore.required = false;
                    if (secondPlaceScore) secondPlaceScore.required = false;
                    if (thirdPlaceScore) thirdPlaceScore.required = false;
                    if (firstPlaceScore) firstPlaceScore.value = 0;
                    if (secondPlaceScore) secondPlaceScore.value = 0;
                    if (thirdPlaceScore) thirdPlaceScore.value = 0;
                }
            }
            
            if (hasPrizeCheckbox) {
                hasPrizeCheckbox.addEventListener('change', togglePrizePlaces);
                togglePrizePlaces();
            }
            
            const coverInput = document.getElementById('coverInput');
            const coverPreview = document.getElementById('coverPreview');
            
            coverInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        coverPreview.innerHTML = '';
                        coverPreview.appendChild(img);
                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'remove-cover';
                        removeBtn.innerHTML = '×';
                        removeBtn.onclick = function(e) {
                            e.stopPropagation();
                            removeCover();
                        };
                        coverPreview.appendChild(removeBtn);
                        coverPreview.classList.add('has-image');
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            const numberInputs = document.querySelectorAll('input[type="number"]');
            numberInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value < 0) this.value = 0;
                });
                input.addEventListener('input', function() {
                    if (this.value < 0) this.value = 0;
                });
            });
            
            const form = document.getElementById('eventForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (hasPrizeCheckbox && hasPrizeCheckbox.checked) {
                        let hasError = false;
                        const firstVal = parseFloat(firstPlaceScore.value);
                        const secondVal = parseFloat(secondPlaceScore.value);
                        const thirdVal = parseFloat(thirdPlaceScore.value);
                        
                        if (isNaN(firstVal) || firstVal < 0) {
                            alert('Пожалуйста, укажите корректное количество баллов для 1 места');
                            firstPlaceScore.focus();
                            hasError = true;
                        } else if (isNaN(secondVal) || secondVal < 0) {
                            alert('Пожалуйста, укажите корректное количество баллов для 2 места');
                            secondPlaceScore.focus();
                            hasError = true;
                        } else if (isNaN(thirdVal) || thirdVal < 0) {
                            alert('Пожалуйста, укажите корректное количество баллов для 3 места');
                            thirdPlaceScore.focus();
                            hasError = true;
                        }
                        
                        if (hasError) {
                            e.preventDefault();
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>