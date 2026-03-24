<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
         .form-registr {
            max-width: 680px;
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
        }

        .gradient-text {
            background: linear-gradient(90deg, #0022CD, #A100FF);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            font-size: 3.15rem;
            font-weight: 800;
            display: inline-block;
            margin-bottom: 24px;
            letter-spacing: -0.01em;
            border-left: 5px solid #0022CD; /* Полоска слева */
            padding-left: 20px;
        }

        form label {
            font-weight: 600;
            color: #1A1F2E;
            display: block;
            margin: 16px 0 6px 0;
            font-size: 0.9rem;
        }

        form input, form select {
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

        form input:focus, form select:focus {
            border-color: #5B4BD6;
        }
        .dynamic-block {
            background: #f8f9ff;
            border-radius: 28px;
            padding: 18px 22px;
            margin: 24px 0 16px;
            border: 1px solid #e2e8ff;
        }

        .dynamic-block h3 {
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
        }

        /* кнопка регистрации */
        .submit-btn {
            background: linear-gradient(90deg, #0022CD, #6b3eff);
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
            margin-top: 32px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 8px 18px rgba(0, 34, 205, 0.25);
            font-family: inherit;
        }

        .submit-btn:hover {
            background: linear-gradient(95deg, #0022CD, #6b3eff);
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
            margin-top: 32px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 8px 18px rgba(0, 34, 205, 0.25);
            font-family: inherit;
            transition: 0.2s;
            transform: scale(0.96);
            
        }

        .auth-links {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 34, 205, 0.1);
        }
    
        .auth-links p {
            color: #1A1F2E;
            font-size: 0.9rem;
        }
    
        .auth-link-btn {
            display: inline-block;
            margin-top: 12px;
            background: transparent;
            border: 1.5px solid #0022CD;
            padding: 10px 28px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            color: #0022CD;
            transition: all 0.2s ease;
        }
    
        .auth-link-btn:hover {
            background: #0022CD;
            color: white;
            transition: 0.2s;
            transform: scale(0.96);
        }

        .footer {
            background:#9FBFFF;
            padding:40px;
            margin-top:40px;
        }

        .footer-logo {
            font-size:2rem;
            font-weight:800;
            background: linear-gradient(90deg,#0022CD,#A100FF);
            -webkit-background-clip:text;
            color:transparent;
            margin-bottom:10px;
        }
        .required {
            color: #F40BA6;
            margin-left: 4px;
        }
    </style> 

    <div class="bg-circle circle-1"></div>
    <div class="bg-circle circle-2"></div>
    <div class="bg-circle circle-3"></div>
    <div class="bg-circle circle-4"></div>

    <header class="header">
        <a href="/" class="logo">EventHub</a>
        <div class="nav-links">
            <a href="Top1.html">Топ участников</a>
            <a href="/events">Мероприятия</a>
            <a href="/organizers">Организаторы</a>
            <a href="/profile" class="profile-link">Личный кабинет</a>
        </div>
    </header>

    <div class="form-registr">

    <form action="/reg" method="POST">
        <span class="gradient-text">Регистрация</span>
        
        <label>Email:<span class="required">*</span></label>
        <input type="email" name="register_email" required>
        <br><br>
        
        <label>Пароль:<span class="required">*</span></label>
        <input type="password" name="register_pass" required>
        <br><br>
        
        <label>Роль:<span class="required">*</span></label>
        <select name="register_role" required>
            <option value="">Выберите роль</option>
            <option value="1">Пользователь</option>
            <option value="2">Организатор</option>
            <option value="3">Кадровый инспектор</option>
        </select>
        <br><br>
        
        <div id="userFields" style="display:none;">
            <h3>Данные пользователя:</h3>
            <label>Фамилия:</label>
            <input type="text" name="user_lastname">
            <br><br>
            
            <label>Имя:</label>
            <input type="text" name="user_name">
            <br><br>
            
            <label>Отчество:</label>
            <input type="text" name="user_midname">
            <br><br>
            
            <label>Дата рождения:<span class="required">*</span></label>
            <input type="date" name="user_bdate"required>
            <br><br>
            
            <label>Город:<span class="required">*</span></label>
            <input type="text" name="user_city"required>
            <br><br>
        </div>
        
        <div id="promoterFields" style="display:none;">
            <h3>Данные оршанизатора:</h3>
            <label>Имя организатора:</label>
            <input type="text" name="promoter_name">
            <br><br>
            
            <label>Город:</label>
            <input type="text" name="promoter_city">
            <br><br>
            
            <label>Тип организатора:</label>
            <select name="promoter_type">
                <option value="">Выберите тип</option>
                <option value="1">Образование</option>
                <option value="2">Госссектор</option>
                <option value="3">Бизнес</option>
                <option value="4">НКО</option>
            </select>
            <br><br>
        </div>
        
        <div id="coordinatorFields" style="display:none;">
            <h3>Данные кадрового инспектора:</h3>
            <label>Фамилия:</label>
            <input type="text" name="talent_coordinator_lastname">
            <br><br>
            
            <label>Имя:</label>
            <input type="text" name="talent_coordinator_name">
            <br><br>
            
            <label>Отчество:</label>
            <input type="text" name="talent_coordinator_midname">
            <br><br>
            
            <label>Имя организации:</label>
            <input type="text" name="talent_coordinator_org">
            <br><br>
            
            <label>Тип :</label>
            <select name="talent_coordinator_type">
                <option value="">Выберите тип</option>
                <option value="1">Государственное учреждение</option>
                <option value="2">Частаная компания</option>
            </select>
            <br><br>
        </div>

        <input type="submit" value="Зарегистрироваться" class="submit-btn">
    </form>

    <div class="auth-links">
                <p>Уже есть аккаунт?</p>
                <a href="/login" class="auth-link-btn">Войти в аккаунт</a>
            </div>
        </form>
    </div>

    <footer class="footer">
    <div class="footer-logo">EventHub</div>
    <div>Молодежная платформа развития потенциала и новых знакомств</div>
    </footer>
</div>

    <script>
        const roleSelect = document.querySelector('select[name="register_role"]');
        const userFields = document.getElementById('userFields');
        const promoterFields = document.getElementById('promoterFields');
        const coordinatorFields = document.getElementById('coordinatorFields');
        
        function toggleFields() {
            userFields.style.display = 'none';
            promoterFields.style.display = 'none';
            coordinatorFields.style.display = 'none';
            
            
            const role = roleSelect.value;
            
            if (role === '1') {
                userFields.style.display = 'block';
            } else if (role === '2') {
                promoterFields.style.display = 'block';
            } else if (role === '3') {
                coordinatorFields.style.display = 'block';
            }
            setRequiredFields(role);
        }
        
        function setRequiredFields(role) {
            const allInputs = document.querySelectorAll('input, select');
            allInputs.forEach(input => {
                if (input.name !== 'register_email' && input.name !== 'register_pass' && input.name !== 'register_role') {
                    input.removeAttribute('required');
                }
            });

            if (role === '1') {
                document.querySelector('input[name="user_lastname"]').setAttribute('required', 'required');
                document.querySelector('input[name="user_name"]').setAttribute('required', 'required');
                document.querySelector('input[name="user_bdate"]').setAttribute('required', 'required');
                document.querySelector('input[name="user_city"]').setAttribute('required', 'required');
            } else if (role === '2') {
                document.querySelector('input[name="promoter_name"]').setAttribute('required', 'required');
                document.querySelector('input[name="promoter_city"]').setAttribute('required', 'required');
                document.querySelector('select[name="promoter_type"]').setAttribute('required', 'required');
            } else if (role === '3') {
                document.querySelector('input[name="talent_coordinator_lastname"]').setAttribute('required', 'required');
                document.querySelector('input[name="talent_coordinator_name"]').setAttribute('required', 'required');
                document.querySelector('input[name="talent_coordinator_org"]').setAttribute('required', 'required');
                document.querySelector('select[name="talent_coordinator_type"]').setAttribute('required', 'required');
            }
        }
        
        roleSelect.addEventListener('change', toggleFields);
    </script>
</body>
</html>